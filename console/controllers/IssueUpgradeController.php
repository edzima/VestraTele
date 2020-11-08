<?php

namespace console\controllers;

use backend\modules\user\models\CustomerUserForm;
use common\models\address\Address as LegacyAddress;
use common\models\address\City;
use common\models\issue\Issue;
use common\models\issue\IssueMeet;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueUser;
use common\models\issue\MeetAddress;
use common\models\meet\MeetForm;
use common\models\user\Customer;
use common\models\user\User;
use edzima\teryt\models\Simc;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\StringHelper;

class IssueUpgradeController extends Controller {

	private $foundedCities = [];

	public function actionWorkers(): void {
		IssueUser::deleteAll(['type' => [IssueUser::TYPE_AGENT, IssueUser::TYPE_TELEMARKETER, IssueUser::TYPE_LAWYER]]);
		$count = 0;
		foreach (Issue::find()
			->select(['id', 'agent_id', 'lawyer_id', 'tele_id'])
			->asArray()
			->batch() as $rows) {
			$users = [];
			foreach ($rows as $row) {
				$issueId = (int) $row['id'];

				if ($row['agent_id'] > 0) {
					$users[] = [
						'issue_id' => $issueId,
						'user_id' => $row['agent_id'],
						'type' => IssueUser::TYPE_AGENT,
					];
				}
				if ($row['lawyer_id'] > 0) {
					$users[] = [
						'issue_id' => $issueId,
						'user_id' => $row['lawyer_id'],
						'type' => IssueUser::TYPE_LAWYER,
					];
				}

				if ($row['tele_id'] > 0) {
					$users[] = [
						'issue_id' => $issueId,
						'user_id' => $row['tele_id'],
						'type' => IssueUser::TYPE_TELEMARKETER,
					];
				}
			}

			$count += Yii::$app->db->createCommand()
				->batchInsert(IssueUser::tableName(), ['issue_id', 'user_id', 'type'], $users)->execute();
		}

		Console::output('Successful imported ' . $count . ' users.');
	}

	public function actionProvision(): void {
		foreach (Issue::find()
			->batch() as $rows) {
			foreach ($rows as $issue) {
				/** @var $issue Issue */
				if (!StringHelper::startsWith($issue->details, 'PROWIZJA -')) {
					$provision = $issue->getProvision();
					if ($provision) {
						$details = [];
						$details[] = 'PROWIZJA - rodzaj: ' . $provision->getTypeName();
						$details[] = 'Podstawa: ' . $provision->getBase();
						$details[] = 'Procent\krotność: ' . $provision->getValue();
						$details[] = $issue->details;
						$issue->details = implode("\n", $details);
						$issue->save(false, ['details']);
					}
				}
			}
		}
	}

	public function actionCheckCustomer(): void {
		foreach (Issue::find()
			->withoutArchives()
			->with('customer')
			->batch() as $rows) {
			foreach ($rows as $issue) {
				/** @var $issue Issue */
				Console::output('Parse issue: ' . $issue->longId);
				if ($issue->customer) {
					if ($issue->customer->getFullName() !== $issue->getClientFullName()) {
						Console::output('Customer: ' . $issue->customer->getFullName());
						Console::output('Old client: ' . $issue->getClientFullName());
						Console::confirm('Not same names: ' . $issue->id);
					}
				} else {
					Console::confirm('Not customer for isssue: ' . $issue->id);
				}
			}
		}
	}

	public function actionPayCalculation(): void {
		$ids = IssuePayCalculation::find()
			->select(['issue_id', 'id'])
			->asArray()
			->all();

		$map = ArrayHelper::map($ids, 'issue_id', 'id');
		foreach ($map as $issueId => $calculationId) {
			IssuePay::updateAll(['calculation_id' => $calculationId], ['issue_id' => $issueId]);
		}
	}

	public function actionCustomer(): void {
		foreach (Issue::find()
			->withoutCustomer()
			->batch() as $rows) {
			foreach ($rows as $issue) {
				/** @var Issue $issue */
				Console::output('------------------------------');
				Console::output('Parse issue: ' . $issue);
				$this->parseClient($issue);
			}
		}
	}

	public function actionVictim(): void {
		foreach (Issue::find()
			->andWhere(['type_id' => 1])
			->withoutArchives()
			->batch() as $rows) {
			foreach ($rows as $issue) {
				/** @var Issue $issue */
				Console::output('------------------------------');
				Console::output('Parse issue: ' . $issue);
				$this->parseVictim($issue);
			}
		}
	}

	public function actionMeet(): void {
		foreach (IssueMeet::find()
			->batch() as $rows) {
			foreach ($rows as $model) {
				/** @var IssueMeet $model */
				Console::output('Parse meet: ' . $model->id);
				$form = new MeetForm();
				$form->setModel($model);
				$legacy = $model->getAddress();
				$form->getAddress()->postal_code = $legacy->cityCode;
				$form->getAddress()->info = $legacy->street;
				$form->getAddress()->city_id = $this->getCityID($legacy);

				if ($form->getAddress()->save()) {
					$customerAddress = $model->addresses[MeetAddress::TYPE_CUSTOMER] ?? new MeetAddress(['type' => MeetAddress::TYPE_CUSTOMER]);
					$customerAddress->meet_id = $model->id;
					$customerAddress->address_id = $form->getAddress()->id;
					$customerAddress->save();
				} else {
					Console::output('Dont save address: ' . $form->getAddress()->attributes);
					Console::output(var_dump($form->getAddress()->attributes));
				}
			}
		}
	}

	private function parseClient(Issue $issue): User {
		$customer = $this->findClient($issue);
		if ($customer !== null) {
			Console::output('Customer already exist: ' . $customer->getFullName());
			$issue->linkUser($customer->id, IssueUser::TYPE_CUSTOMER);
			return $customer;
		}
		$customer = new CustomerUserForm();
		$customer->sendEmail = false;
		$customer->email = $issue->client_email;
		$customer->getProfile()->firstname = $issue->client_first_name;
		$customer->getProfile()->lastname = $issue->client_surname;
		$customer->getProfile()->phone = $issue->client_phone_1;
		$customer->getProfile()->phone_2 = $issue->client_phone_2;

		$this->setAddress($customer, $issue->getClientAddress());

		if (!$customer->validate()) {
			$this->fixModel($customer);
		}
		$customer->roles = [Customer::ROLE_CUSTOMER];

		if ($customer->save()) {
			Console::output('Success create Customer:' . $customer->getModel()->getFullName());
			$issue->linkUser($customer->getModel()->id, IssueUser::TYPE_CUSTOMER);
		} else {
			Console::output('Customer dont save.');
			Console::output(var_export($customer->getErrors()));
			Console::output(var_export($customer->getProfile()->getErrors()));
		}
		return $customer->getModel();
	}

	private function parseVictim(Issue $issue): void {
		if (!$this->clientIsVictim($issue) || !$issue->getUsers()->withType(IssueUser::TYPE_VICTIM)->exists()) {
			Console::output('Client is not victim');
			if (empty($issue->victim_first_name) || empty($issue->victim_surname)) {
				return;
			}
			$victim = $this->findVictim($issue);

			if ($victim !== null) {
				Console::output('Victim already exist: ' . $victim->getFullName());
				if ($victim->id !== $issue->customer->id) {
					$issue->linkUser($victim->id, IssueUser::TYPE_VICTIM);
				}
			} else {

				$victim = new CustomerUserForm();
				$victim->sendEmail = false;
				$victim->email = $issue->victim_email;
				$victim->getProfile()->firstname = $issue->victim_first_name;
				$victim->getProfile()->lastname = $issue->victim_surname;
				$victim->getProfile()->phone = $issue->victim_phone;
				$this->setAddress($victim, $issue->getVictimAddress());
				if (!$victim->validate()) {
					$this->fixModel($victim);
				}
				if ($victim->save()) {
					Console::output('Success create Victim:' . $victim->getModel()->getFullName());
					$issue->linkUser($victim->getModel()->id, IssueUser::TYPE_VICTIM);
				} else {
					Console::output('Victim dont save.');
					Console::output(var_export($victim->getErrors()));
				}
			}
		}
	}

	private function findClient(Issue $model): ?User {
		$user = $this->findByMail($model, 'client_email');
		if ($user) {
			Console::output('Find by email: ' . $user->email);
			return $user;
		}
		$user = $this->findByPhone($model, 'client_phone_1');
		if ($user) {
			Console::output('Find by phone: ' . $user->profile->phone);
			return $user;
		}
		$users = $this->findByNames($model->client_first_name, $model->client_surname);
		if (empty($users)) {
			return null;
		}
		if (count($users) === 1) {
			$user = reset($users);
			Console::output('Find one by names: ' . $user->getFullName());
			return $user;
		}
		$address = new LegacyAddress();
		$address->cityCode = $model->client_city_code;
		$address->street = $model->client_street;
		$users = $this->filterByAddress($users, $address);
		if (count($users) === 1) {
			$user = reset($users);
			Console::output('Find one from filtered address: ' . $user->getFullName());
			return $user;
		}
		if (count($users) > 1) {
			Console::output(var_dump($users));
			Console::confirm('Find more by one users with same address');
		}
		return null;
	}

	private function findVictim(Issue $model): ?User {
		$user = $this->findByMail($model, 'victim_email');
		if ($user) {
			Console::output('Find by email: ' . $user->email);
			return $user;
		}
		$user = $this->findByPhone($model, 'victim_phone');
		if ($user) {
			Console::output('Find by phone: ' . $user->profile->phone);
			return $user;
		}
		if (empty($model->victim_first_name) || empty($model->victim_surname)) {
			return null;
		}
		$users = $this->findByNames($model->victim_first_name, $model->victim_surname);
		if (empty($users)) {
			return null;
		}
		$address = new LegacyAddress();
		$address->cityCode = $model->victim_city_code;
		$address->street = $model->victim_street;
		$users = $this->filterByAddress($users, $address);
		if (count($users) === 1) {
			$user = reset($users);
			Console::output('Find one from filtered address: ' . $user->getFullName());
			return $user;
		}
		if (count($users) > 1) {
			Console::output(var_dump($users));
			Console::confirm('Find more by one users with same address');
		}
		return null;
	}

	/**
	 * @param User[] $users
	 * @param LegacyAddress $address
	 * @return User|null
	 */
	private function filterByAddress(array $users, LegacyAddress $legacyAddress): array {
		return array_filter($users, function (User $user) use ($legacyAddress): bool {
			if (!$user->homeAddress) {
				Console::output('User has not home address: ' . $user->getFullName());
				return false;
			}
			return $user->homeAddress->info === $legacyAddress->street && $user->homeAddress->postal_code === $legacyAddress->cityCode;
		});
	}

	private function findByNames(string $firstName, string $lastname): array {
		return User::find()
			->joinWith('userProfile')
			->andWhere([
				'firstname' => $firstName,
				'lastname' => $lastname,
			])
			->all();
	}

	private function findByMail(Issue $model, string $emailAttribute): ?User {
		if (!empty($model->{$emailAttribute}) && $model->validate([$emailAttribute])) {
			Console::output('Try Find by email: ' . $model->{$emailAttribute});
			/** @noinspection PhpIncompatibleReturnTypeInspection */
			return User::find()
				->andWhere(['email' => $model->{$emailAttribute}])
				->one();
		}
		return null;
	}

	private function findByPhone(Issue $model, string $phoneAttribute): ?User {
		if (!empty($model->{$phoneAttribute}) && $model->validate([$phoneAttribute])) {
			Console::output('Try Find by phone: ' . $model->{$phoneAttribute});

			/** @noinspection PhpIncompatibleReturnTypeInspection */
			return User::find()
				->joinWith('userProfile')
				->andWhere([
					'phone' => $model->{$phoneAttribute},
				])
				->one();
		}
		return null;
	}

	private function clientIsVictim(Issue $model): bool {
		return $model->client_surname === $model->victim_surname
			&& $model->client_first_name === $model->victim_first_name;
	}

	private function fixModel(CustomerUserForm $model): void {
		//	Console::output(var_dump($model->getErrors()));
		//	Console::output(var_dump($model->getProfile()->getErrors()));

		if ($model->getProfile()->hasErrors('phone')) {
			if (!empty($model->getProfile()->phone)) {
				Console::output('Has error for phone: ' . $model->getProfile()->phone);
				if (Console::confirm('Insert valid phone number', true)) {
					$model->getProfile()->phone = Console::prompt('');
				} elseif (Console::confirm('Move to other?', true)) {
					$model->getProfile()->other .= 'Phone: ' . $model->getProfile()->phone;
					$model->getProfile()->phone = null;
				} else {
					$model->getProfile()->phone = null;
				}
			} else {
				$model->getProfile()->phone = null;
			}
		}

		if ($model->getProfile()->hasErrors('phone_2')) {
			if (!empty($model->getProfile()->phone_2)) {
				Console::output('Has error for phone2: ' . $model->getProfile()->phone_2);
				if (Console::confirm('Insert valid phone number', true)) {
					$model->getProfile()->phone_2 = Console::prompt('');
				} elseif (Console::confirm('Move to other?', true)) {
					$model->getProfile()->other .= 'Phone2: ' . $model->getProfile()->phone;
					$model->getProfile()->phone_2 = null;
				} else {
					$model->getProfile()->phone_2 = null;
				}
			} else {
				$model->getProfile()->phone_2 = null;
			}
		}
		if ($model->hasErrors('username')) {
			$this->fixUsername($model);
		}
	}

	private function fixUsername(CustomerUserForm $model): void {
		$model->username = Console::prompt('Insert new username ' . $model->username . ' from: ' . $model->getProfile()->firstname . ' ' . $model->getProfile()->lastname);
		while (!$model->validate(['username'])) {
			$this->fixUsername($model);
		}
	}

	private function setAddress(CustomerUserForm $model, LegacyAddress $address): void {
		$model->getHomeAddress()->postal_code = $address->cityCode;
		$model->getHomeAddress()->info = $address->street;
		$model->getHomeAddress()->city_id = $this->getCityID($address);
	}

	private function getCityID(LegacyAddress $address): ?int {
		if ($address->cityId === null) {
			return null;
		}
		if (isset($this->foundedCities[$address->cityId])) {
			return $this->foundedCities[$address->cityId];
		}
		$id = $this->findCityId($address);
		if ($id !== null) {
			$this->foundedCities[$address->cityId] = $id;
		}
		return $this->foundedCities[$address->cityId];
	}

	private function findCityId(LegacyAddress $address): ?int {
		$city = $address->getCity();
		if ($city) {
			if ($city->name === City::NOT_EXIST_NAME) {
				return null;
			}
			$models = Simc::find()
				->withName($city->name)
				->andWhere(['region_id' => $city->wojewodztwo_id])
				->all();
			if (empty($models)) {
				Console::output('Not found city: ' . $city->name . ' in province: ' . $city->province->name);
				while (($model = Simc::findOne(Console::prompt('Insert city ID'))) === null) {
					Console::output('Invalid ID');
				}
				return $model->id;
			}
			if (count($models) > 1) {
				Console::output('------------------------');
				Console::output('Find more than one city.');
				Console::output('City: ' . $city->name);
				if ($address->getSubProvince()) {
					Console::output('District: ' . $address->getProvince()->name);
				}
				if ($address->getSubProvince()) {
					Console::output('Commune: ' . $address->getSubProvince()->name);
				}

				$ids = [];
				$names = [];
				foreach ($models as $model) {

					if ($model->name === $city->name) {
						$names[] = $model->id;
						if (
							(
								$address->provinceId === $model->district_id
								&& ($address->subProvinceId === null || (int) $address->subProvinceId === (int) $model->commune_id)
							)
							|| (
								$address->getProvince()
								&& $address->getProvince()->name === $model->terc->district->name)
						) {
							return $model->id;
						}
					}

					$ids[] = $model->id;
					Console::output('ID: ' . $model->id . ' - ' . $model->getNameWithRegionAndDistrict());
				}
				if (count($names) === 1) {
					return reset($names);
				}
				while (!in_array(($id = Console::prompt('Select city ID')), $ids)) {
					Console::output('Invalid ID');
				}
				return $id;
			}
			return reset($models)->id;
		}
		return null;
	}
}

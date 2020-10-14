<?php

namespace console\controllers;

use backend\modules\user\models\CustomerUserForm;
use common\models\address\Address as LegacyAddress;
use common\models\address\City;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\Customer;
use common\models\user\User;
use edzima\teryt\models\Simc;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class IssueUpgradeController extends Controller {

	private $foundedCities = [];

	public function actionFind(): void {
		$issue = new Issue();
		$issue->client_phone_1 = '+48 511 858 183';
		$client = $this->findClient($issue);
		if ($client === null) {
			Console::output('Not found');
		} else {
			Console::output($client);
		}
	}

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

	public function actionCustomer(): void {

		//	Customer::deleteAll();

		foreach (Issue::find()
			//	->withoutArchives()
			->withoutCustomer()
			->batch() as $rows) {
			foreach ($rows as $issue) {
				/** @var Issue $issue */
				Console::output('Parse issue: ' . $issue);
				$this->parseClient($issue);
				$this->parseVictim($issue);
			}
		}
	}

	private function parseClient(Issue $issue): void {
		$customer = $this->findClient($issue);
		if ($customer !== null) {
			Console::output('Customer already exist: ' . $customer->getFullName());
			$issue->linkUser($customer->id, IssueUser::TYPE_CUSTOMER);
		} else {
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
			if (!$this->clientIsVictim($issue)) {
				$customer->roles = [Customer::ROLE_CUSTOMER];
			} else {
				$customer->roles = [Customer::ROLE_CUSTOMER, Customer::ROLE_VICTIM];
			}
			if ($customer->save()) {
				Console::output('Success create Customer:' . $customer->getModel()->getFullName());
				$issue->linkUser($customer->getModel()->id, IssueUser::TYPE_CUSTOMER);
			} else {
				Console::output('Customer dont save.');
				Console::output(var_export($customer->getErrors()));
				Console::output(var_export($customer->getProfile()->getErrors()));
			}
		}
	}

	private function parseVictim(Issue $issue): void {
		if (!$this->clientIsVictim($issue)) {
			Console::output('Client is not victim');
			if (empty($issue->victim_first_name) && empty($issue->victim_surname)) {
				return;
			}
			$victim = $this->findVictim($issue);

			if ($victim !== null) {
				Console::output('Victim already exist: ' . $victim->getFullName());
				$issue->linkUser($victim->id, IssueUser::TYPE_VICTIM);
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
		$users = $this->filterByAddress($users, $model->getClientAddress());
		if (count($users) === 1) {
			$user = reset($users);
			Console::output('Find one from filtered address: ' . $user->getFullName());
			return $user;
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
		if (count($users) === 1) {
			$user = reset($users);
			Console::output('Find one by names: ' . $user->getFullName());
			return $user;
		}
		$users = $this->filterByAddress($users, $model->getClientAddress());
		if (count($users) === 1) {
			$user = reset($users);
			Console::output('Find one from filtered address: ' . $user->getFullName());
			return $user;
		}
		Console::output(var_export($users));
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
			return User::find()
				->andWhere(['email' => $model->{$emailAttribute}])
				->one();
		}
		return null;
	}

	private function findByPhone(Issue $model, string $phoneAttribute): ?User {
		if (!empty($model->{$phoneAttribute}) && $model->validate([$phoneAttribute])) {
			Console::output('Try Find by phone: ' . $model->{$phoneAttribute});

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
		return $model->client_surname === $model->victim_surname && $model->client_first_name === $model->victim_first_name;
	}

	private function fixModel(CustomerUserForm $model): void {
		//	Console::output(var_dump($model->getErrors()));
		//	Console::output(var_dump($model->getProfile()->getErrors()));

		if ($model->getProfile()->hasErrors('phone')) {
			if (!empty($model->getProfile()->phone)) {
				if (Console::confirm('Move to other: ' . $model->getProfile()->phone, true)) {
					$model->getProfile()->other = 'Phone: ' . $model->getProfile()->phone;
					$model->getProfile()->phone = null;
				} else {
					if (Console::confirm('Set as null?', true)) {
						$model->getProfile()->phone = null;
					} else {
						$model->getProfile()->phone = Console::prompt('Insert valid phone ' . $model->getProfile()->phone);
					}
				}
			} else {
				$model->getProfile()->phone = null;
			}
		}
		if ($model->getProfile()->hasErrors('phone_2')) {
			if (!empty($model->getProfile()->phone_2)) {
				if (Console::confirm('Move to other>: ' . $model->getProfile()->phone_2, true)) {
					$model->getProfile()->other .= 'Phone 2: ' . $model->getProfile()->phone;
					$model->getProfile()->phone_2 = null;
				} else {
					if (Console::confirm('Set as null?', true)) {
						$model->getProfile()->phone_2 = null;
					} else {
						$model->getProfile()->phone_2 = Console::prompt('Insert valid phone 2 ' . $model->getProfile()->phone_2);
					}
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
		$model->getAddress()->postal_code = $address->cityCode;
		$model->getAddress()->info = $address->street;
		$model->getAddress()->city_id = $this->getCityID($address);
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
				foreach ($models as $model) {

					if ($model->name === $city->name) {
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

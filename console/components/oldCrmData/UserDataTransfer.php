<?php

namespace console\components\oldCrmData;

use backend\models\UserForm;
use common\models\User;
use common\models\UserProfile;
use console\components\oldCrmData\exceptions\InvalidModelData;
use Yii;
use yii\helpers\ArrayHelper;

class UserDataTransfer extends DataTransfer {

	public $columns = [
		'id',
		'login',
		'first_name',
		'last_name',
		'phone',
		'email',
		'boss',
		'position',
	];
	public $oldTableName = '{{%users}}';
	public $model = User::class;

	private $transferedData = [];
	private $usersIds = [];

	private const ROLES_MAP = [
		1 => User::ROLE_AGENT,
		2 => User::ROLE_MANAGER,
		3 => User::ROLE_ASSOCIATE_DIRECTOR,
		4 => User::ROLE_DIRECTOR,
		5 => User::ROLE_LAYER,
		6 => User::ROLE_GENERAL_DIRECTOR,
	];

	private const DUPLICATED_USERS_IDS = [
		121 => 72,
		146 => 104,
	];

	public function init() {
		parent::init();
		$this->buildModel = function (array $data): void {
			$email = $this->getEmail($data);
			$userModel = $this->findUserByEmail($email);
			if ($userModel === null) {
				$userModel = $this->findUserByUsername(trim($data['login']));
				if ($userModel === null) {
					Yii::info('Create user: ' . $email, __CLASS__);
					$userModel = $this->createUser($data);
				}
			}
			$this->updateOldIdColumn($userModel, (int) $data['id']);
			$profile = $userModel->userProfile;
			$this->updateProfile($profile, $data);
			$this->transferedData[(int) $userModel->id] = $data;
		};
	}

	public function transfer(): void {
		parent::transfer();
		$this->updateBosses();
	}

	/**
	 * Create User from related array from db.
	 *
	 * @param array $data
	 * @return User
	 */
	private function createUser(array $data): User {
		$model = new UserForm();
		$model->username = $data['login'];
		$model->password = $this->createPassword($data);
		$model->email = $this->getEmail($data);
		$model->roles = (array) static::ROLES_MAP[$data['position']];
		$model->status = User::STATUS_ACTIVE;

		if ($model->validate() && $model->save()) {
			return $model->getModel();
		}
		throw new InvalidModelData($model, $data);
	}

	private function getEmail(array $data): string {
		$email = trim($data['email']);
		if ($email === 'brak@vestra.info') {
			$email = trim($data['login']) . '@vestra.info';
		}
		return $email;
	}

	private function createPassword(array $data): string {
		$pass = trim($data['login']);
		if (strlen($pass) > 6) {
			return $pass;
		}
		return strtolower(trim($data['first_name'])) . strtolower(trim($data['last_name']));
	}

	private function updateProfile(UserProfile $profile, array $data): void {
		$profile->firstname = trim($data['first_name']);
		$profile->lastname = trim($data['last_name']);
		$profile->phone = $data['phone'];
		$profile->save(false);
	}

	public function updateBosses(): void {
		$bosses = [];
		$newIds = [];
		foreach ($this->transferedData as $userId => $data) {
			$newIds[$data['id']] = $userId;
			$bosses[(int) $data['boss']][] = $userId;
		}
		foreach ($bosses as $boss => $ids) {
			User::updateAll(['boss' => $newIds[$boss]], ['id' => $ids]);
		}
	}

	/**
	 * @param string $email
	 * @return User|null
	 */
	private function findUserByEmail(string $email): ?User {
		return User::findOne(['email' => $email]);
	}

	private function findUserByUsername(string $name): ?User {
		return User::findByUsername($name);
	}

	public function getUserId(int $oldId): int {
		if (isset(static::DUPLICATED_USERS_IDS[$oldId])) {
			$oldId = static::DUPLICATED_USERS_IDS[$oldId];
		}
		return (int) $this->getUserIds()[$oldId];
	}

	private function getUserIds(): array {
		if (empty($this->usersIds)) {
			$this->usersIds = ArrayHelper::map(User::find()
				->select('id,old_id')
				->where('old_id is not null')
				->asArray()
				->all(),
				'old_id',
				'id');
		}
		return $this->usersIds;
	}

}
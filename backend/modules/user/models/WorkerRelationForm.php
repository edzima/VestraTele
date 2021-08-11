<?php

namespace backend\modules\user\models;

use common\models\user\UserRelation;
use common\models\user\Worker;
use Yii;
use yii\base\Model;

class WorkerRelationForm extends Model {

	public string $type = '';
	public string $userId = '';
	public array $toUsersIds = [];

	public function rules(): array {
		return [
			[['type', 'userId', 'toUsersIds'], 'required'],
			['type', 'string'],
			['userId', 'integer'],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			[['userId'], 'in', 'range' => array_keys(static::getUsersNames())],
			[['toUsersIds'], 'in', 'range' => array_keys(static::getUsersNames()), 'allowArray' => true],
		];
	}

	public function attributeLabels(): array {
		return [
			'userId' => Yii::t('backend', 'User'),
			'toUsersIds' => Yii::t('backend', 'To Users'),
			'type' => Yii::t('backend', 'Type'),
		];
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}

		UserRelation::deleteAll([
			'type' => $this->type,
			'user_id' => $this->userId,
		]);

		$rows = [];
		$time = time();
		foreach ($this->toUsersIds as $id) {
			$rows[] = [
				'type' => $this->type,
				'user_id' => $this->userId,
				'to_user_id' => $id,
				'created_at' => $time,
				'updated_at' => $time,
			];
		}
		if (!empty($rows)) {
			UserRelation::getDb()
				->createCommand()
				->batchInsert(
					UserRelation::tableName(),
					[
						'type',
						'user_id',
						'to_user_id',
						'created_at',
						'updated_at',
					],
					$rows)
				->execute();
		}
		return true;
	}

	public static function getTypesNames(): array {
		$types = UserRelation::getTypesNames();
		unset($types[UserRelation::TYPE_SUPERVISOR]);
		return $types;
	}

	public static function getUsersNames(): array {
		return Worker::usernames();
	}
}

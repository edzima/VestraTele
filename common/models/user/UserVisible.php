<?php

namespace common\models\user;

use common\models\user\query\UserQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_visible".
 *
 * @property int $user_id
 * @property int $to_user_id
 * @property int $status
 *
 * @property User $toUser
 * @property User $user
 */
class UserVisible extends ActiveRecord {

	public const STATUS_VISIBLE = 1;
	public const STATUS_HIDDEN = 10;

	private static $USERS = [];

	/**
	 * @param int $user_id
	 * @return int[] visible users IDs.
	 */
	public static function visibleUsers(int $user_id): array {
		return static::users($user_id)[static::STATUS_VISIBLE] ?? [];
	}

	private static function users(int $user_id): array {
		if (!isset(static::$USERS[$user_id])) {
			$users = static::find()
				->select(['to_user_id', 'status'])
				->andWhere(['user_id' => $user_id])
				->asArray()
				->all();
			$statuses = [];
			foreach ($users as $data) {
				$statuses[(int) $data['status']][] = (int) $data['to_user_id'];
			}
			static::$USERS[$user_id] = $statuses;
		}
		return static::$USERS[$user_id];
	}

	/**
	 * @param int $user_id
	 * @return int[] hidden users IDs.
	 */
	public static function hiddenUsers(int $user_id): array {
		return static::users($user_id)[static::STATUS_HIDDEN] ?? [];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%user_visible}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'to_user_id', 'status'], 'required'],
			[['user_id', 'to_user_id', 'status'], 'integer'],
			[['user_id', 'to_user_id'], 'unique', 'targetAttribute' => ['user_id', 'to_user_id']],
			[['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['to_user_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
		];
	}

	public static function getStatusesNames(): array {
		return [
			static::STATUS_VISIBLE => Yii::t('common', 'Visible User'),
			static::STATUS_HIDDEN => Yii::t('common', 'Hidden User'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'user_id' => Yii::t('common', 'Who'),
			'to_user_id' => Yii::t('common', 'Whom'),
			'status' => 'Status',
		];
	}

	public function getStatusName(): string {
		return static::getStatusesNames()[$this->status];
	}

	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function getToUser(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'to_user_id']);
	}

}

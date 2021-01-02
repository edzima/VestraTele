<?php

namespace common\models\user;

use common\models\user\query\UserQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "customer_trait".
 *
 * @property int $user_id
 * @property int $trait_id
 *
 * @property User $user
 */
class UserTrait extends ActiveRecord {

	public const TRAIT_BAILIFF = 1;
	public const TRAIT_LIABILITIES = 2;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return 'customer_trait';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'trait_id'], 'required'],
			[['user_id', 'trait_id'], 'integer'],
			[['user_id', 'trait_id'], 'unique', 'targetAttribute' => ['user_id', 'trait_id']],
			['trait_id', 'in', 'range' => array_keys(static::getNames())],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'user_id' => Yii::t('common', 'User ID'),
			'trait_id' => Yii::t('common', 'Trait ID'),
		];
	}

	public function getName(): string {
		return static::getNames()[$this->trait_id];
	}

	public function getUser(): ActiveQuery {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public static function getNames(): array {
		return [
			static::TRAIT_BAILIFF => Yii::t('common', 'bailiff'),
			static::TRAIT_LIABILITIES => Yii::t('common', 'liabilities'),
		];
	}

	public static function assignUser(int $userId, array $traits, bool $withDelete = true):void{
		codecept_debug("start");
		if ($withDelete) {
			static::deleteAll(['user_id' => $userId]);
		}
		if (empty($traits)){
			return;
		}
		$userTraits = [];
		foreach ($traits as $trait) {
			$userTraits[] = [
				'user_id' => $userId,
				'trait_id' => $trait,
			];
		}
		$out = static::getDb()->createCommand()->batchInsert(UserTrait::tableName(),['user_id', 'trait_id'], $userTraits)->execute();
		codecept_debug($userTraits);
	}
}

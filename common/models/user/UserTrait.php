<?php

namespace common\models\user;

use common\models\user\query\UserQuery;
use Yii;
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
	public const TRAIT_DISABILITY_RESULT_OF_CASE = 3;

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

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getUser(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public static function getNames(): array {
		return [
			static::TRAIT_BAILIFF => Yii::t('common', 'Bailiff'),
			static::TRAIT_LIABILITIES => Yii::t('common', 'Liabilities'),
			static::TRAIT_DISABILITY_RESULT_OF_CASE => Yii::t('common', 'Disability result of case'),
		];
	}

	/**
	 * @param int $userId
	 * @param int[] $traitsIds
	 * @param bool $withDelete
	 * @throws \yii\db\Exception
	 */
	public static function assignUser(int $userId, array $traitsIds): void {
		if (empty($traitsIds)) {
			static::unassignUser($userId);
			return;
		}
		$userTraits = [];
		foreach ($traitsIds as $id) {
			$userTraits[] = [
				'user_id' => $userId,
				'trait_id' => $id,
			];
		}
		static::getDb()->createCommand()->batchInsert(self::tableName(), ['user_id', 'trait_id'], $userTraits)->execute();
	}

	public static function unassignUser(int $userId): void {
		static::deleteAll(['user_id' => $userId]);
	}
}

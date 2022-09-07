<?php

namespace common\models\user;

use common\models\user\query\UserQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "trait_user".
 *
 * @property int $user_id
 * @property int $trait_id
 *
 * @property User $user
 * @property UserTrait $trait
 */
class UserTraitAssign extends ActiveRecord {

	public const TRAIT_BAILIFF = 150;
	public const TRAIT_COMMISSION_REFUND = 200;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%user_trait_assign}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'trait_id'], 'required'],
			[['user_id', 'trait_id'], 'integer'],
			[['user_id', 'trait_id'], 'unique', 'targetAttribute' => ['user_id', 'trait_id']],
			[['trait_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserTrait::class, 'targetAttribute' => ['trait_id' => 'id']],
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

	/**
	 * Gets query for [[Trait]].
	 *
	 * @return ActiveQuery
	 */
	public function getTrait() {
		return $this->hasOne(UserTrait::class, ['id' => 'trait_id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getUser(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function getName(): string {
		return UserTrait::getNames()[$this->trait_id];
	}

	/**
	 * @param int $userId
	 * @param int[] $traitsIds
	 * @param bool $withDelete
	 * @throws Exception
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
		static::getDb()->createCommand()
			->batchInsert(static::tableName(), ['user_id', 'trait_id'], $userTraits)
			->execute();
	}

	public static function unassignUser(int $userId): void {
		static::deleteAll(['user_id' => $userId]);
	}
}

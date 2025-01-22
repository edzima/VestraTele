<?php

namespace common\modules\court\modules\spi\models\auth;

use common\models\user\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "spi_user_auth".
 *
 * @property int $id
 * @property int $user_id
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $last_action_at
 * @property string $username
 * @property string $password
 *
 * @property User $user
 */
class SpiUserAuth extends ActiveRecord {

	public function behaviors(): array {
		return [
			TimestampBehavior::class,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%spi_user_auth}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'username', 'password'], 'required'],
			[['user_id', 'created_at', 'updated_at', 'last_action_at'], 'integer'],
			[['username', 'password'], 'string', 'max' => 255],
			[['user_id'], 'unique'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('spi', 'ID'),
			'user_id' => Yii::t('spi', 'User ID'),
			'created_at' => Yii::t('spi', 'Created At'),
			'updated_at' => Yii::t('spi', 'Updated At'),
			'last_action_at' => Yii::t('spi', 'Last Action At'),
			'username' => Yii::t('spi', 'Username'),
			'password' => Yii::t('spi', 'Password'),
		];
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public static function findByUserId($id): ?SpiUserAuth {
		return static::find()
			->andWhere(['user_id' => $id])
			->one();
	}

	public function encryptPassword(string $password, string $key): void {
		$this->password = Yii::$app->security
			->encryptByPassword(
				$password,
				$this->getModelPasswordKey($key)
			);
	}

	public function decryptPassword(string $key): string {
		return Yii::$app->security
			->decryptByPassword(
				$this->password,
				$this->getModelPasswordKey($key)
			);
	}

	protected function getModelPasswordKey(string $key): string {
		return $key;
	}

	public function touchLastActionAt(): void {
		$this->updateAttributes(['last_action_at' => time()]);
	}

}

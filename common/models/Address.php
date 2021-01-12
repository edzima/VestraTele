<?php

namespace common\models;

use common\models\issue\IssueMeet;
use common\models\user\query\UserQuery;
use common\models\user\User;
use edzima\teryt\models\query\SimcQuery;
use edzima\teryt\models\Simc;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "address".
 *
 * @property int $id
 * @property int|null $city_id
 * @property string|null $postal_code
 * @property string|null $info
 *
 * @property Simc $city
 *
 * @property-read User[] $users
 * @property-read IssueMeet[] $meets
 */
class Address extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%address}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['postal_code', 'info'], 'trim'],
			[
				'city_id', 'required', 'enableClientValidation' => false, 'when' => function (): bool {
				return empty($this->postal_code) && empty($this->info);
			},
			],
			[
				'postal_code', 'required', 'enableClientValidation' => false, 'when' => function (): bool {
				return empty($this->city_id) && empty($this->info);
			},
			],
			[
				'info', 'required', 'enableClientValidation' => false, 'when' => function (): bool {
				return empty($this->city_id) && empty($this->postal_code);
			},
			],
			[['city_id'], 'integer'],
			[['postal_code'], 'string', 'max' => 6],
			[['info'], 'string', 'max' => 100],
			[['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Simc::class, 'targetAttribute' => ['city_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('address', 'ID'),
			'city_id' => Yii::t('address', 'City'),
			'postal_code' => Yii::t('address', 'Postal Code'),
			'info' => Yii::t('address', 'Info'),
		];
	}

	public function getCity(): SimcQuery {
		return $this->hasOne(Simc::class, ['id' => 'city_id']);
	}

	public function getUsers(): UserQuery {
		return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('{{%user_address}}', ['address_id' => 'id']);
	}


	public function getMeets(): ActiveQuery {
		return $this->hasMany(IssueMeet::class, ['id' => 'meet_id'])->viaTable('{{%meet_address}}', ['address_id' => 'id']);
	}

	public function getCityFullName(): string {
		return $this->city->getAddress()->fullName;
	}

}

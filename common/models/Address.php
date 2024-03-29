<?php

namespace common\models;

use common\models\user\query\UserQuery;
use common\models\user\User;
use common\modules\lead\models\Lead;
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
 * @property-read Lead[] $leads
 */
class Address extends ActiveRecord {

	public string $formName = 'address';
	public const SCENARIO_NOT_REQUIRED = 'not-required';

	public function formName(): string {
		return $this->formName;
	}

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
				'city_id', 'required',
				'enableClientValidation' => false,
				'when' => function (): bool {
					return empty($this->postal_code) && empty($this->info);
				},
				'except' => static::SCENARIO_NOT_REQUIRED,
			],
			[
				'postal_code', 'required',
				'enableClientValidation' => false,
				'when' => function (): bool {
					return empty($this->city_id) && empty($this->info);
				},
				'except' => static::SCENARIO_NOT_REQUIRED,
			],
			[
				'info', 'required',
				'enableClientValidation' => false,
				'when' => function (): bool {
					return empty($this->city_id) && empty($this->postal_code);
				},
				'except' => static::SCENARIO_NOT_REQUIRED,
			],
			[['city_id'], 'integer'],
			[['postal_code'], 'string', 'max' => 6],
			['postal_code', 'match', 'pattern' => '/^[0-9]{2}-[0-9]{3}/'],
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

	public function getLeads(): ActiveQuery {
		return $this->hasMany(Lead::class, ['id' => 'lead_id'])->viaTable('{{%lead_address}}', ['address_id' => 'id']);
	}

	public function getCityFullName(): string {
		return $this->city->getAddress()->fullName;
	}

	public function getCityWithPostalCode(bool $withRegionAndProvince = false, bool $strongPostalCodeTag = true): ?string {
		$city = $this->city;
		if ($city === null) {
			return Yii::$app->formatter->asCityCode(null, $this->postal_code);
		}
		$cityName = $withRegionAndProvince ? $city->nameWithRegionAndDistrict : $city->name;
		return Yii::$app->formatter->asCityCode($cityName, $this->postal_code, $strongPostalCodeTag);
	}

	public function isEmpty(): bool {
		return empty($this->city_id)
			&& empty($this->info)
			&& empty($this->postal_code);
	}

}

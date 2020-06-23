<?php

namespace common\models\issue;

use common\models\address\Address;
use common\models\address\City;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_pay_city".
 *
 * @property int $city_id
 * @property string $phone
 * @property string $bank_transfer_at
 * @property string $direct_at
 *
 * @property City $city
 */
class IssuePayCity extends ActiveRecord {

	/**
	 * @var Address
	 */
	private $address;

	public function __toString() {
		if ($this->city !== null) {
			return $this->city->name;
		}
		return 'usunięto miasto';
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName() {
		return 'issue_pay_city';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			['city_id', 'unique'],
			[['bank_transfer_at', 'direct_at'], 'safe'],
			['bank_transfer_at', 'required', 'when' => function (IssuePayCity $model) { return empty($model->direct_at); }, 'enableClientValidation' => false, 'message' => 'Chociaż jedna data płatności powinna zostać uzupełniona'],
			['direct_at', 'required', 'when' => function (IssuePayCity $model) { return empty($model->bank_transfer_at); }, 'enableClientValidation' => false, 'message' => 'Chociaż jedna data płatności powinna zostać uzupełniona'],
			[['phone'], 'string', 'max' => 15],
			[['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'city_id' => 'Miejscowość',
			'city' => 'Miejscowość',
			'phone' => 'Telefon',
			'bank_transfer_at' => 'Przelewy',
			'direct_at' => 'Gotówka',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCity() {
		return $this->hasOne(City::class, ['id' => 'city_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::class, ['id' => 'city_id']);
	}

	public function getAddress(): Address {
		if ($this->address === null) {
			if ($this->city !== null) {
				$this->address = Address::createFromCity($this->city);
			} else {
				$this->address = new Address();
				$this->address->uniqueCityClass = static::class;
			}
		}
		return $this->address;
	}

	public function hasTransferDate(): bool {
		return $this->hasBankTransferDate() || $this->hasDirectDate();
	}

	public function hasDirectDate(): bool {
		return $this->direct_at !== null;
	}

	public function hasBankTransferDate(): bool {
		return $this->bank_transfer_at !== null;
	}

}

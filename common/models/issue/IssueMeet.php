<?php

namespace common\models\issue;

use common\models\Campaign;
use common\models\City;
use common\models\Gmina;
use common\models\Powiat;
use common\models\query\UserQuery;
use common\models\User;
use common\models\Wojewodztwa;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "issue_meet".
 *
 * @property int $id
 * @property int $type_id
 * @property string $phone
 * @property string $client_name
 * @property string $client_surname
 * @property int $tele_id
 * @property int $agent_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $date_at
 * @property string $details
 * @property int $status
 * @property int $city_id
 * @property int $sub_province_id
 * @property string $street
 * @property int $campaign_id
 *
 * @property User $agent
 * @property User $tele
 * @property IssueType $type
 * @property-read int $stateId
 * @property-read City $city
 * @property-read Wojewodztwa $state
 * @property-read Powiat $province
 * @property-read Gmina $subProvince
 * @property-read Campaign $campaign
 */
class IssueMeet extends ActiveRecord {

	public const STATUS_NEW = 1;
	public const STATUS_RENEW_CONTACT = 5;

	public const STATUS_ESTABLISHED = 10;
	public const STATUS_SIGNED_CONTRACT = 20;
	public const STATUS_NOT_ELIGIBLE = 30;
	public const STATUS_NOT_SIGNED = 40;
	public const STATUS_CONTACT_AGAIN = 50;
	public const  STATUS_ARCHIVE = 200;

	public function behaviors(): array {
		return [
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('CURRENT_TIMESTAMP'),
				'createdAtAttribute' => false,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return 'issue_meet';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['type_id', 'phone', 'client_name', 'status', 'campaign_id'], 'required'],
			[['type_id', 'tele_id', 'agent_id', 'status', 'city_id', 'sub_province_id', 'campaign_id'], 'integer'],
			[['created_at', 'updated_at', 'date_at'], 'safe'],
			[['details', 'street'], 'string'],
			[['phone'], 'string', 'max' => 15],
			[['client_name'], 'string', 'max' => 20],
			[['client_surname'], 'string', 'max' => 30],
			['status', 'in', 'range' => array_keys(static::getStatusNames())],
			[['campaign_id'], 'exist', 'skipOnError' => true, 'targetClass' => Campaign::class, 'targetAttribute' => ['campaign_id' => 'id']],
			[['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['agent_id' => 'id']],
			[['tele_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['tele_id' => 'id']],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueType::class, 'targetAttribute' => ['type_id' => 'id']],
			[['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
			[['sub_province_id'], 'exist', 'skipOnError' => true, 'targetClass' => Gmina::class, 'targetAttribute' => ['sub_province_id' => 'id']],

		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'type_id' => 'Typ',
			'phone' => 'Telefon',
			'client_name' => 'Imie',
			'client_surname' => 'Nazwisko',
			'tele_id' => 'Tele',
			'agent_id' => 'Agent',
			'created_at' => 'Data leada',
			'updated_at' => 'Edytowano',
			'date_at' => 'Data spotkania',
			'details' => 'Szczegóły',
			'status' => 'Status',
			'street' => 'Ulica',
			'city' => 'Miasto',
			'state' => 'Województwo',
			'province' => 'Powiat',
			'subProvince' => 'Gmina',
			'campaign_id' => 'Kampania',
			'campaign' => 'Kampania',
			'statusName' => 'Status',
		];
	}

	public function getClientFullName(): string {
		return trim($this->client_surname . ' ' . $this->client_name);
	}

	public function getAgent(): UserQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(User::class, ['id' => 'agent_id']);
	}

	public function getTele(): UserQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(User::class, ['id' => 'tele_id']);
	}

	public function getType(): ActiveQuery {
		return $this->hasOne(IssueType::class, ['id' => 'type_id']);
	}

	public function getCampaign(): ActiveQuery {
		return $this->hasOne(Campaign::class, ['id' => 'campaign_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCity() {
		return $this->hasOne(City::class, ['id' => 'city_id'])->cache();
	}

	public function getStateId(): ?int {
		if ($this->city === null) {
			return null;
		}
		return $this->city->wojewodztwo_id;
	}

	public function getProvinceId(): ?int {
		if ($this->city === null) {
			return null;
		}
		return $this->province->id;
	}

	public function getState() {
		return $this->hasOne(Wojewodztwa::class, ['id' => 'wojewodztwo_id'])->via('city');
	}

	public function getProvince() {
		return $this->hasOne(Powiat::class, ['id' => 'powiat_id', 'wojewodztwo_id' => 'wojewodztwo_id'])->via('city');
	}

	public function getSubProvince() {
		return $this->hasOne(Gmina::class, ['id' => 'sub_province_id']);
	}

	public function isNew(): bool {
		return (int) $this->status === static::STATUS_NEW;
	}

	public function getStatusName(): string {
		return static::getStatusNames()[$this->status];
	}

	public static function getStatusNames(): array {
		return [
			static::STATUS_NEW => 'Nowe',
			static::STATUS_RENEW_CONTACT => 'Nieodbiera',
			static::STATUS_ESTABLISHED => 'Umówione',
			static::STATUS_SIGNED_CONTRACT => 'Umowa',
			static::STATUS_NOT_ELIGIBLE => 'Niekwalifikuje się',
			static::STATUS_NOT_SIGNED => 'Niepodpisane',
			static::STATUS_CONTACT_AGAIN => 'Ponowić',
			static::STATUS_ARCHIVE => 'Archiwum',
		];
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(IssueType::find()
			->andWhere(['meet' => true])
			->all(), 'id', 'short_name');
	}

	public static function getCampaignNames(): array {
		return ArrayHelper::map(Campaign::find()->all(), 'id', 'name');
	}

	public function isForUser(int $userId): bool {
		return $this->tele_id === $userId || $this->agent_id === $userId;
	}
}

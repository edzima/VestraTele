<?php

namespace common\models\issue;

use common\models\Address;
use common\models\address\Address as LegacyAddress;
use common\models\address\City;
use common\models\address\Province;
use common\models\address\State;
use common\models\address\SubProvince;
use common\models\Campaign;
use common\models\user\query\UserQuery;
use common\models\user\Worker;
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
 * @property int $agent_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $date_at
 * @property string $date_end_at
 * @property string $details
 * @property int $status
 * @property int $city_id
 * @property int $sub_province_id
 * @property string $street
 * @property int $campaign_id
 * @property string $email
 *
 * @property Worker $agent
 * @property IssueType $type
 * @property-read int $stateId
 * @property-read City $city
 * @property-read State $state
 * @property-read Province $province
 * @property-read SubProvince $subProvince
 * @property-read Campaign $campaign
 * @property-read MeetAddress[] $addresses
 * @property-read Address|null $customerAddress
 */
class IssueMeet extends ActiveRecord {

	public const STATUS_NEW = 1;
	public const STATUS_RENEW_CONTACT = 5;

	public const STATUS_ESTABLISHED = 10;
	public const STATUS_SIGNED_CONTRACT = 20;
	public const STATUS_NOT_ELIGIBLE = 30;
	public const STATUS_NOT_SIGNED = 40;
	public const STATUS_CONTACT_AGAIN = 50;
	public const STATUS_ARCHIVE = 200;

	protected static $TYPES = [];

	/**
	 * @var LegacyAddress
	 */
	private $address;

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
			[['type_id', 'client_name', 'status'], 'required'],
			[['type_id', 'agent_id', 'status', 'city_id', 'sub_province_id', 'campaign_id'], 'integer'],
			[['created_at', 'updated_at', 'date_at', 'date_end_at'], 'safe'],
			[['details', 'street', 'email'], 'string'],
			['email', 'email'],
			[['phone'], 'string', 'max' => 20],
			[['client_name'], 'string', 'max' => 20],
			[['client_surname'], 'string', 'max' => 30],
			['status', 'in', 'range' => array_keys(static::getStatusNames())],
			[['campaign_id'], 'exist', 'skipOnError' => true, 'targetClass' => Campaign::class, 'targetAttribute' => ['campaign_id' => 'id']],
			[['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Worker::class, 'targetAttribute' => ['agent_id' => 'id']],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueType::class, 'targetAttribute' => ['type_id' => 'id']],
			[['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
			[['sub_province_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubProvince::class, 'targetAttribute' => ['sub_province_id' => 'id']],

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
			'agent_id' => 'Agent',
			'created_at' => 'Data leada',
			'date_at' => 'Data Wysyłki/Spotkania/Akcji',
			'date_end_at' => 'Koniec Wysyłki/Spotkania/Akcji',
			'updated_at' => 'Edytowano',
			'email' => 'E-mail',
			'details' => 'Szczegóły',
			'status' => 'Status',
			'street' => 'Ulica',
			'city' => 'Miasto',
			'state' => 'Województwo',
			'province' => 'Powiat',
			'subProvince' => 'Gmina',
			'campaign_id' => 'Kampania',
			'campaign' => 'Kampania',
			'campaignName' => 'Kampania',
			'statusName' => 'Status',
			'type' => 'Typ',
		];
	}

	public function getAddress(): LegacyAddress {
		if ($this->address === null || $this->address->cityId !== $this->city_id) {
			$address = new LegacyAddress();
			if ($this->city) {
				$address->setCity($this->city);
			}
			if ($this->sub_province_id) {
				$address->subProvinceId = $this->sub_province_id;
			}
			$address->street = $this->street;
			$this->address = $address;
		}
		return $this->address;
	}

	public function getClientFullName(): string {
		return trim($this->client_surname . ' ' . $this->client_name);
	}

	public function getCampaignName(): string {
		if ($this->hasCampaign()) {
			return $this->campaign->name;
		}
		return 'Własna';
	}

	protected function getAddresses(): ActiveQuery {
		return $this->hasMany(MeetAddress::class, ['meet_id' => 'id'])->indexBy('type');
	}

	public function getCustomerAddress(): ?Address {
		return $this->addresses[MeetAddress::TYPE_CUSTOMER]->address ?? null;
	}

	public function getAgent(): UserQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(Worker::class, ['id' => 'agent_id']);
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
		return $this->hasOne(State::class, ['id' => 'wojewodztwo_id'])->via('city');
	}

	public function getProvince() {
		return $this->hasOne(Province::class, ['id' => 'powiat_id', 'wojewodztwo_id' => 'wojewodztwo_id'])->via('city');
	}

	public function getSubProvince() {
		return $this->hasOne(SubProvince::class, ['id' => 'sub_province_id']);
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

	public static function getTypesNames(bool $short = false): array {
		$name = $short ? 'short_name' : 'name';
		return ArrayHelper::map(static::getTypes(), 'id', $name);
	}

	private static function getTypes(): array {
		if (empty(static::$TYPES)) {
			static::$TYPES = IssueType::find()
				->andWhere(['meet' => true])
				->all();
		}
		return static::$TYPES;
	}

	public static function getCampaignNames(): array {
		return ArrayHelper::map(Campaign::find()
			->orderBy(['default' => SORT_DESC])->all(), 'id', 'name');
	}

	public function hasCampaign(): bool {
		return !empty($this->campaign_id) && $this->campaign !== null;
	}

	public function isArchived(): bool {
		return $this->status === static::STATUS_ARCHIVE;
	}
}

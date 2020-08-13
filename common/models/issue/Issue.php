<?php

namespace common\models\issue;

use app\models\IssueUser;
use common\behaviors\DateIDBehavior;
use common\models\address\Address;
use common\models\address\City;
use common\models\address\Province;
use common\models\address\State;
use common\models\address\SubProvince;
use common\models\entityResponsible\EntityResponsible;

use common\models\User;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "issue".
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $date
 * @property int $agent_id
 * @property string $client_first_name
 * @property string $client_surname
 * @property string $client_phone_1
 * @property string $client_phone_2
 * @property string $client_email
 * @property int $client_city_id
 * @property int $client_subprovince_id
 * @property string $client_city_code
 * @property string $client_street
 * @property string $victim_first_name
 * @property string $victim_surname
 * @property string $victim_email
 * @property int $victim_subprovince_id
 * @property int $victim_city_id
 * @property string $victim_city_code
 * @property string $victim_street
 * @property string $victim_phone
 * @property string $details
 * @property int $provision_type
 * @property string $provision_value
 * @property string $provision_base
 * @property int $stage_id
 * @property int $type_id
 * @property int $entity_responsible_id
 * @property string $archives_nr
 * @property int $lawyer_id
 * @property int $tele_id
 * @property string $accident_at
 * @property bool $payed
 * @property int $pay_city_id
 * @property string $stage_change_at
 *
 * @property string $longId
 * @property int $clientStateId
 * @property int $clientProvinceId
 * @property City $clientCity
 * @property City $victimCity
 * @property User $agent
 * @property User $lawyer
 * @property User|null $tele
 * @property IssuePay[] $pays
 * @property EntityResponsible $entityResponsible
 * @property IssueStage $stage
 * @property IssueType $type
 * @property IssueNote[] $issueNotes
 * @property Provision $provision
 * @property SubProvince $clientSubprovince
 * @property SubProvince $victimSubprovince
 * @property IssuePayCity $payCity
 * @property IssuePayCalculation $payCalculation
 */
class Issue extends ActiveRecord {

	private const DEFAULT_PROVISION = Provision::TYPE_PERCENTAGE;
	private $provision;

	public $victim_state_id;
	public $victim_province_id;

	/**
	 * @var Address
	 */
	private $clientAddress;

	/** @var Address */
	private $victimAddress;

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return 'issue';
	}

	public function behaviors(): array {
		return [
			DateIDBehavior::class,
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('CURRENT_TIMESTAMP'),
			],
		];
	}

	public function beforeValidate() {
		$this->client_city_id = $this->getClientAddress()->cityId;
		$this->client_street = $this->getClientAddress()->street;
		$this->client_city_code = $this->getClientAddress()->cityCode;
		return parent::beforeValidate();
	}

	public function beforeSave($insert) {
		if (isset($this->dirtyAttributes['stage_id'])) {
			if (empty($this->stage_change_at)) {
				$this->stage_change_at = date(DATE_ATOM);
			}
		}
		$this->client_city_id = $this->getClientAddress()->cityId;
		$this->client_city_code = $this->getClientAddress()->cityCode;
		$this->client_subprovince_id = $this->getClientAddress()->subProvinceId;
		$this->client_street = $this->getClientAddress()->street;

		$this->victim_city_id = $this->getVictimAddress()->cityId !== $this->client_city_id ? $this->getVictimAddress()->cityId : null;
		$this->victim_city_code = $this->getVictimAddress()->cityCode !== $this->client_city_code ? $this->getVictimAddress()->cityCode : null;
		$this->victim_subprovince_id = $this->getVictimAddress()->subProvinceId !== $this->client_subprovince_id ? $this->getVictimAddress()->subProvinceId : null;
		$this->victim_street = $this->getVictimAddress()->street !== $this->client_street ? $this->getVictimAddress()->street : null;

		return parent::beforeSave($insert);
	}

	public function __toString(): string {
		return $this->longId;
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['created_at', 'updated_at'], 'safe'],
			[
				[
					'agent_id', 'client_first_name', 'client_surname', 'client_city_id', 'client_city_code',
					'client_street', 'provision_type', 'stage_id', 'type_id', 'entity_responsible_id', 'date', 'lawyer_id',
				], 'required',
			],
			[
				[
					'agent_id', 'tele_id', 'lawyer_id', 'client_subprovince_id', 'client_city_id', 'victim_subprovince_id',
					'victim_city_id', 'provision_type', 'stage_id', 'type_id', 'entity_responsible_id', 'id', 'pay_city_id',
				], 'integer',
			],
			[
				[
					'client_first_name', 'client_surname', 'client_email', 'client_street', 'victim_first_name',
					'victim_surname', 'victim_email', 'victim_street',
				], 'string', 'max' => 255,
			],
			[['client_phone_1', 'client_phone_2', 'victim_phone'], 'string', 'max' => 20],
			[['client_phone_1', 'client_phone_2', 'victim_phone'], PhoneValidator::class, 'country' => 'PL'],

			[['client_email', 'victim_email'], 'email'],
			[['client_city_code', 'victim_city_code'], 'string', 'max' => 6],
			[['victim_first_name', 'victim_surname', 'victim_phone', 'victim_email', 'victim_city_id', 'victim_street'], 'default', 'value' => null],
			[['archives_nr'], 'string', 'max' => 10],
			[['details'], 'string'],
			[['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['agent_id' => 'id']],
			[['lawyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['lawyer_id' => 'id']],
			[['tele_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['tele_id' => 'id']],
			[['client_city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['client_city_id' => 'id']],
			[['pay_city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['pay_city_id' => 'id']],
			[['entity_responsible_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntityResponsible::class, 'targetAttribute' => ['entity_responsible_id' => 'id']],
			[['stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueStage::class, 'targetAttribute' => ['stage_id' => 'id']],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueType::class, 'targetAttribute' => ['type_id' => 'id']],
			[['provision_base', 'provision_value'], 'number', 'min' => 1],
			[['provision_value'], 'number', 'max' => 1000],
			['provision_type', 'in', 'range' => array_keys(Provision::getTypesNames())],
			[
				'archives_nr',
				'required',
				'when' => static function (Issue $model) {
					return $model->isArchived();
				},
				'whenClient' => 'function(attribute, value){
					return isArchived();
				}',
			],
			[
				'pay_city_id',
				'required',
				'when' => static function (Issue $model) {
					return $model->isPositiveDecision();
				},
				'whenClient' => 'function(attribute, value){
					return isPositiveDecision();
				}',
			],
			[['client_email', 'victim_email'], 'email'],
			['payed', 'boolean'],
			['payed', 'default', 'value' => false],
			['stage_id', 'filter', 'filter' => 'intval'],
			[['date', 'accident_at', 'stage_change_at'], 'date', 'format' => DATE_ATOM],
		];
	}

	public function getClientStateId(): ?int {
		return $this->getClientAddress()->stateId;
	}

	public function getClientProvinceId(): ?int {
		return $this->getClientAddress()->provinceId;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'longId' => 'ID',
			'created_at' => 'Dodano',
			'updated_at' => 'Edycja',
			'date' => 'Data podpisania',
			'agent_id' => 'Agent',
			'client_first_name' => 'Imie',
			'client_surname' => 'Nazwisko',
			'client_phone_1' => 'Tel. klienta',
			'client_phone_2' => 'Tel. 2 klienta',
			'client_email' => 'Email',
			'client_city_id' => 'Miasto',
			'client_city_code' => 'Kod pocztowy',
			'client_street' => 'Ulica klienta',
			'victim_first_name' => 'Imie',
			'victim_surname' => 'Nazwisko',
			'victim_phone' => 'Tel.',
			'victim_email' => 'Email',
			'victim_city_id' => 'Miasto',
			'victim_city_code' => 'Kod pocztowy',
			'victim_street' => 'Ulica',
			'details' => 'Szczegoły',
			'provision_base' => 'Wartość roszczenia',
			'provision_type' => 'Rodzaj',
			'provision_value' => 'Procent \ Krotność',
			'stage_id' => 'Etap',
			'type_id' => 'Typ',
			'entity_responsible_id' => 'Podmiot odpowiedzialny',
			'archives_nr' => 'Archiwum',
			'clientState' => 'Region klienta',
			'clientCity' => 'Miasto klienta',
			'payed' => 'Opłacono',
			'lawyer_id' => 'Prawnik',
			'tele_id' => 'Telemarketer',
			'accident_at' => 'Data wypadku',
			'entityResponsibleDetails' => 'Podmiot odpowiedzialny',
			'pay_city_id' => 'Miasto wypłacające',
			'payCity' => 'Miasto wypłacające',
			'stage_change_at' => 'Data etapu',

		];
	}

	public function getClientState() {
		return $this->hasOne(State::class, ['id' => 'wojewodztwo_id'])->via('clientCity');
	}

	public function getClientProvince() {
		return $this->hasOne(Province::class, ['id' => 'powiat_id', 'wojewodztwo_id' => 'wojewodztwo_id'])->via('clientCity');
	}


	public function getClientAddress(): Address {
		if ($this->clientAddress === null) {
			$address = new Address();
			$address->formName = 'clientAddress';
			if ($this->clientCity) {
				$address->setCity($this->clientCity);
			}
			if ($this->hasClientSubprovince()) {
				$address->setSubProvince($this->clientSubprovince);
			}
			$address->customRules = [
				[['street', 'cityCode'], 'required'],
			];
			$address->street = $this->client_street;
			$address->cityCode = $this->client_city_code;
			$this->clientAddress = $address;
		}
		return $this->clientAddress;
	}

	public function getVictimAddress(): Address {
		if ($this->victimAddress === null) {
			$address = new Address();
			if ($this->victimCity) {
				$address->setCity($this->victimCity);
			} elseif ($this->clientCity) {
				$address->setCity($this->clientCity);
			}
			if ($this->hasVictimSubprovince()) {
				$address->setSubProvince($this->victimSubprovince);
			}
			$address->requiredCity = false;
			$address->formName = 'victimAddress';
			$address->cityCode = $this->getVictimCityCode();
			$address->street = $this->getVictimStreet();
			$this->victimAddress = $address;
		}
		return $this->victimAddress;
	}

	public function getClientSubprovince() {
		return $this->hasOne(SubProvince::class, ['id' => 'client_subprovince_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getClientCity() {
		return $this->hasOne(City::class, ['id' => 'client_city_id'])->cache();
	}

	public function getVictimSubprovince() {
		return $this->hasOne(SubProvince::class, ['id' => 'victim_subprovince_id']);
	}

	public function getVictimFirstName(): string {
		if ($this->victim_first_name === null) {
			return $this->client_first_name;
		}
		return $this->victim_first_name;
	}

	public function getVictimSurname(): string {
		if ($this->victim_surname === null) {
			return $this->client_surname;
		}
		return $this->victim_surname;
	}

	public function getVictimPhone(): string {
		if ($this->victim_phone === null) {
			return $this->client_phone_1;
		}
		return $this->victim_phone;
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getVictimCity() {
		if ($this->victim_city_id === null) {
			return $this->getClientCity();
		}
		return $this->hasOne(City::class, ['id' => 'victim_city_id']);
	}

	public function getVictimCityCode(): ?string {
		if ($this->victim_city_code === null) {
			return $this->client_city_code;
		}
		return $this->victim_city_code;
	}

	public function getVictimStreet(): ?string {
		if ($this->victim_street === null) {
			return $this->client_street;
		}
		return $this->victim_street;
	}

	public function getAgent() {
		return $this->hasOne(User::class, ['id' => 'agent_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEntityResponsible() {
		return $this->hasOne(EntityResponsible::class, ['id' => 'entity_responsible_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getStage() {
		return $this->hasOne(IssueStage::class, ['id' => 'stage_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getType() {
		return $this->hasOne(IssueType::class, ['id' => 'type_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssueNotes(): IssueNoteQuery {
		return $this->hasMany(IssueNote::class, ['issue_id' => 'id'])->with('user')->orderBy('created_at DESC');
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPayCity() {
		return $this->hasOne(IssuePayCity::class, ['city_id' => 'pay_city_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPayCalculation() {
		return $this->hasOne(IssuePayCalculation::class, ['issue_id' => 'id']);
	}

	public function getPays(): IssuePayQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(IssuePay::class, ['issue_id' => 'id'])
			->orderBy(IssuePay::tableName() . '.deadline_at ASC, ' . IssuePay::tableName() . '.pay_at DESC');
	}

	public function isArchived(): bool {
		return (int) $this->stage_id === IssueStage::ARCHIVES_ID;
	}

	public function isPositiveDecision(): bool {
		return (int) $this->stage_id === IssueStage::POSITIVE_DECISION_ID;
	}

	public function isAccident(): bool {
		return (int) $this->type_id === IssueType::ACCIDENT_ID;
	}

	public function isSpa(): bool {
		return (int) $this->type_id === IssueType::SPA_ID;
	}

	public function getProvision(): Provision {
		if ($this->provision === null) {
			if ($this->isNewRecord) {
				$type = static::DEFAULT_PROVISION;
			} else {
				$type = $this->provision_type;
			}
			$this->provision = new Provision($type, [
				'base' => (float) $this->provision_base,
				'value' => (float) $this->provision_value,
			]);
		}
		return $this->provision;
	}

	public function getLawyer() {
		return IssueUser::getIssueUsersByTypes([User::ROLE_LAWYER])->one();
	}

	public function getTele() {
		return IssueUser::getIssueUsersByTypes([User::ROLE_TELEMARKETER])->one();
	}

	public function hasTele(): bool {
		return $this->tele_id !== null && $this->tele !== null;
	}

	public function hasLawyer(): bool {
		return $this->lawyer !== null;
	}

	public function hasProvision(): bool {
		return $this->provision_base > 0;
	}

	public function getClientFullName(): string {
		return $this->client_surname . ' ' . $this->client_first_name;
	}

	public function getVictimFullName(): string {
		return $this->victim_surname . ' ' . $this->victim_first_name;
	}

	public function hasClientSubprovince(): bool {
		return $this->client_subprovince_id !== null && $this->clientSubprovince !== null;
	}

	public function hasVictimSubprovince(): bool {
		return $this->victim_subprovince_id !== null && $this->victimSubprovince !== null;
	}

	public function markAsUpdate(): void {
		$this->touch('updated_at');
	}

	public function hasPayCalculation(): bool {
		return $this->payCalculation !== null;
	}

	/**
	 * @inheritdoc
	 * @return IssueQuery the active query used by this AR class.
	 */
	public static function find(): IssueQuery {
		return new IssueQuery(get_called_class());
	}

}

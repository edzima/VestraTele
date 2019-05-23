<?php

namespace common\models\issue;

use common\behaviors\DateIDBehavior;
use common\models\City;
use common\models\Gmina;
use common\models\Powiat;
use common\models\User;
use common\models\Wojewodztwa;
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
 *
 * @property int $clientStateId
 * @property int $clientProvinceId
 * @property City $clientCity
 * @property City $victimCity
 * @property User $agent
 * @property User $lawyer
 * @property User $tele
 * @property IssuePay[] $pays
 * @property IssueEntityResponsible $entityResponsible
 * @property IssueStage $stage
 * @property IssueType $type
 * @property IssueNote[] $issueNotes
 * @property Provision $provision
 * @property Gmina $clientSubprovince
 * @property Gmina $victimSubprovince
 * @property string $longId
 * @property bool $payed
 */
class Issue extends ActiveRecord {

	private const DEFAULT_PROVISION = Provision::TYPE_PERCENTAGE;
	private $provision;

	public $victim_state_id;
	public $victim_province_id;

	public const PAYED_NOT = 1;
	public const PAYED_PART = 2;
	public const PAYED_ALL = 3;

	public static function payStatuses(): array {
		return [
			static::PAYED_NOT => 'Brak',
			static::PAYED_PART => 'Częściowa',
			static::PAYED_ALL => 'Całość',
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'issue';
	}

	public function behaviors() {
		return [
			DateIDBehavior::class,
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('CURRENT_TIMESTAMP'),
			],
		];
	}

	public function __toString(): string {
		return $this->longId;
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['created_at', 'updated_at'], 'safe'],
			[['agent_id', 'client_first_name', 'client_surname', 'client_phone_1', 'client_city_id', 'client_city_code', 'client_street', 'provision_type', 'stage_id', 'type_id', 'entity_responsible_id', 'date', 'lawyer_id'], 'required'],
			[['agent_id', 'tele_id', 'lawyer_id', 'client_subprovince_id', 'client_city_id', 'victim_subprovince_id', 'victim_city_id', 'provision_type', 'stage_id', 'type_id', 'entity_responsible_id', 'id'], 'integer'],
			[['provision_value', 'provision_base'], 'number'],
			[['client_first_name', 'client_surname', 'client_email', 'client_street', 'victim_first_name', 'victim_surname', 'victim_email', 'victim_street'], 'string', 'max' => 255],
			[['client_phone_1', 'client_phone_2', 'victim_phone'], 'string', 'max' => 15],
			[['client_email', 'victim_email'], 'email'],
			[['client_city_code', 'victim_city_code'], 'string', 'max' => 6],
			[['victim_first_name', 'victim_surname', 'victim_phone', 'victim_email', 'victim_city_id', 'victim_street'], 'default', 'value' => null],
			[['archives_nr'], 'string', 'max' => 10],
			[['details'], 'string'],
			[['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['agent_id' => 'id']],
			[['lawyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['lawyer_id' => 'id']],
			[['tele_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['tele_id' => 'id']],
			[['client_city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['client_city_id' => 'id']],
			[['entity_responsible_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueEntityResponsible::class, 'targetAttribute' => ['entity_responsible_id' => 'id']],
			[['stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueStage::class, 'targetAttribute' => ['stage_id' => 'id']],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueType::class, 'targetAttribute' => ['type_id' => 'id']],
			[['victim_city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['victim_city_id' => 'id']],
			[['provision_base', 'provision_value'], 'number', 'min' => 1],
			[['provision_value'], 'number', 'max' => 1000],
			['provision_type', 'in', 'range' => array_keys(Provision::getTypesNames())],
			[
				'archives_nr',
				'required',
				'when' => function (Issue $model) {
					return $model->isArchived();
				},
				'whenClient' => 'function(attribute, value){
					return isArchived();
	
				}',
			],
			[['client_email', 'victim_email'], 'email'],
			['payed', 'boolean'],
			['payed', 'default', 'value' => false],
			['date', 'date', 'format' => DATE_ATOM],
		];
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
		];
	}

	public function getClientStateId(): ?int {
		if ($this->clientCity === null) {
			return null;
		}
		return $this->clientCity->wojewodztwo_id;
	}

	public function getClientState() {
		return $this->hasOne(Wojewodztwa::class, ['id' => 'wojewodztwo_id'])->via('clientCity');
	}

	public function getClientProvince() {
		return $this->hasOne(Powiat::class, ['id' => 'powiat_id', 'wojewodztwo_id' => 'wojewodztwo_id'])->via('clientCity');
	}

	public function getClientSubprovince() {
		return $this->hasOne(Gmina::class, ['id' => 'client_subprovince_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getClientCity() {
		return $this->hasOne(City::class, ['id' => 'client_city_id'])->cache();
	}

	public function getClientProvinceId(): ?int {
		if ($this->clientCity === null) {
			return null;
		}
		return $this->clientCity->powiat_id;
	}

	public function getVictimStateId(): int {
		if ($this->victim_city_id === null) {
			$this->clientStateId;
		}
		return $this->victimCity->wojewodztwo_id;
	}

	public function getVictimState() {
		return $this->hasOne(Wojewodztwa::class, ['id' => 'wojewodztwo_id'])->via('victimCity');
	}

	public function getVictimProvince() {
		return $this->hasOne(Powiat::class, ['id' => 'powiat_id', 'wojewodztwo_id' => 'wojewodztwo_id'])->via('victimCity');
	}

	public function getVictimSubprovince() {
		return $this->hasOne(Gmina::class, ['id' => 'victim_subprovince_id']);
	}

	public function getVictimProvinceId(): int {
		if ($this->victim_city_id === null) {
			$this->clientProvinceId;
		}
		return $this->victimCity->powiat_id;
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

	public function getVictimCityCode(): string {
		if ($this->victim_city_code === null) {
			return $this->client_city_code;
		}
		return $this->victim_city_code;
	}

	public function getVictimStreet(): string {
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
		return $this->hasOne(IssueEntityResponsible::class, ['id' => 'entity_responsible_id']);
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
	public function getIssueNotes() {
		return $this->hasMany(IssueNote::class, ['issue_id' => 'id'])->with('user')->orderBy('created_at DESC');
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPays() {
		return $this->hasMany(IssuePay::class, ['issue_id' => 'id'])->orderBy(IssuePay::tableName() . '.date DESC');
	}

	public function isArchived(): bool {
		return $this->stage_id == IssueStage::ARCHIVES_ID;
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
		return $this->hasOne(User::class, ['id' => 'lawyer_id']);
	}

	public function getTele() {
		return $this->hasOne(User::class, ['id' => 'tele_id']);
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

	public function markAsPayed() {
		$this->updateAttributes(['payed' => true]);
	}

	public function unmarkAsPayed() {
		$this->updateAttributes(['payed' => false]);
	}

	public function isPayed(): bool {
		return (bool) $this->payed === true;
	}

	public function getPayStatus(): string {
		if ($this->isPayed()) {
			return static::payStatuses()[static::PAYED_ALL];
		}
		if (empty($this->pays)) {
			return static::payStatuses()[static::PAYED_NOT];
		}
		return static::payStatuses()[static::PAYED_PART];
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

	/**
	 * @inheritdoc
	 * @return IssueQuery the active query used by this AR class.
	 */
	public static function find() {
		return new IssueQuery(get_called_class());
	}
}

<?php

namespace common\models\issue;

use common\behaviors\DateIDBehavior;
use common\models\address\Address as LegacyAddress;
use common\models\address\City;
use common\models\address\Province;
use common\models\address\State;
use common\models\address\SubProvince;
use common\models\entityResponsible\EntityResponsible;
use common\models\entityResponsible\EntityResponsibleQuery;
use common\models\issue\query\IssueNoteQuery;
use common\models\issue\query\IssuePayCalculationQuery;
use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\issue\query\IssueStageQuery;
use common\models\issue\query\IssueUserQuery;
use common\models\user\Customer;
use common\models\user\query\UserQuery;
use common\models\user\User;
use common\models\user\Worker;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "issue".
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
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
 * @property bool $payed
 * @property string|null $signing_at
 * @property string|null $type_additional_date_at
 * @property string $stage_change_at
 * @property string|null $signature_act
 *
 * @property string $longId
 * @property int $clientStateId
 * @property int $clientProvinceId
 * @property City $clientCity
 * @property City $victimCity
 * @property Worker $agent
 * @property Worker $lawyer
 * @property-read Customer $customer
 * @property Worker|null $tele
 * @property IssuePay[] $pays
 * @property EntityResponsible $entityResponsible
 * @property IssueStage $stage
 * @property IssueType $type
 * @property IssueNote[] $issueNotes
 * @property Provision $provision
 * @property SubProvince $clientSubprovince
 * @property SubProvince $victimSubprovince
 * @property IssuePayCalculation[] $payCalculations
 * @property-read Summon[] $summons
 * @property-read IssueUser[] $users
 * @property-read IssueCost[] $costs
 */
class Issue extends ActiveRecord implements IssueInterface {

	use IssueTrait;

	private const DEFAULT_PROVISION = Provision::TYPE_PERCENTAGE;

	/* @var LegacyAddress */
	private $clientAddress;
	/* @var LegacyAddress */
	private $victimAddress;

	/* @var Provision */
	private $provision;

	public function __toString(): string {
		return $this->longId;
	}

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

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['created_at', 'updated_at'], 'safe'],
			[['stage_id', 'type_id', 'entity_responsible_id',], 'required',],
			[['stage_id', 'type_id', 'entity_responsible_id'], 'integer'],
			[['details', 'signature_act'], 'string'],
			['archives_nr', 'unique'],
			[['entity_responsible_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntityResponsible::class, 'targetAttribute' => ['entity_responsible_id' => 'id']],
			[['stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueStage::class, 'targetAttribute' => ['stage_id' => 'id']],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueType::class, 'targetAttribute' => ['type_id' => 'id']],
			//@todo remove this rules after create customers from production Issues.
			[['client_email', 'victim_email'], 'email'],
			[['client_phone_1', 'client_phone_2', 'victim_phone'], PhoneValidator::class, 'country' => 'PL'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'longId' => 'ID',
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'details' => Yii::t('common', 'Details'),
			'stage_id' => Yii::t('common', 'Stage'),
			'type_id' => Yii::t('common', 'Type'),
			'stage' => Yii::t('common', 'Stage'),
			'type' => Yii::t('common', 'Type'),
			'entity_responsible_id' => Yii::t('common', 'Entity responsible'),
			'entityResponsible' => Yii::t('common', 'Entity responsible'),
			'signing_at' => Yii::t('common', 'Signing at'),
			'archives_nr' => Yii::t('common', 'Archives'),
			'type_additional_date_at' => $this->type
				? Yii::t('common', 'Date at ({type})', ['type' => $this->type->name])
				: Yii::t('common', 'Additional Date for Type'),
			'stage_change_at' => Yii::t('common', 'Stage date'),
			'signature_act' => Yii::t('common', 'Signature act'),
			'customer' => IssueUser::getTypesNames()[IssueUser::TYPE_CUSTOMER],
		];
	}

	public function getCustomer(): UserQuery {
		/** @todo change to customer after UserQuery join with assignment table */
		return $this->getUserType(IssueUser::TYPE_CUSTOMER, User::class);
	}

	public function getAgent(): UserQuery {
		return $this->getUserType(IssueUser::TYPE_AGENT, User::class);
	}

	public function getLawyer(): UserQuery {
		return $this->getUserType(IssueUser::TYPE_LAWYER, User::class);
	}

	public function getTele(): UserQuery {
		return $this->getUserType(IssueUser::TYPE_TELEMARKETER, User::class);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	protected function getUserType(string $type, string $userClass = User::class, callable $callable = null): UserQuery {
		return $this->hasOne($userClass, ['id' => 'user_id'])->via('users', function (IssueUserQuery $query) use ($type, $callable) {
			$query->alias($type);
			$query->withType($type);
			if ($callable !== null) {
				$callable($query, $type);
			}
		});
	}

	public function getCosts(): ActiveQuery {
		return $this->hasMany(IssueCost::class, ['issue_id' => 'id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getUsers(): IssueUserQuery {
		return $this->hasMany(IssueUser::class, ['issue_id' => 'id']);
	}

	public function getClientStateId(): ?int {
		return $this->getClientAddress()->stateId;
	}

	public function getClientProvinceId(): ?int {
		return $this->getClientAddress()->provinceId;
	}

	/** @deprecated */
	public function getClientState(): ActiveQuery {
		return $this->hasOne(State::class, ['id' => 'wojewodztwo_id'])->via('clientCity');
	}

	/** @deprecated */
	public function getClientProvince(): ActiveQuery {
		return $this->hasOne(Province::class, ['id' => 'powiat_id', 'wojewodztwo_id' => 'wojewodztwo_id'])->via('clientCity');
	}

	public function getClientAddress(): LegacyAddress {
		if ($this->clientAddress === null) {
			$address = new LegacyAddress();
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

	public function getVictimAddress(): LegacyAddress {
		if ($this->victimAddress === null) {
			$address = new LegacyAddress();
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

	/** @deprecated */
	public function getClientSubprovince(): ActiveQuery {
		return $this->hasOne(SubProvince::class, ['id' => 'client_subprovince_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getClientCity() {
		return $this->hasOne(City::class, ['id' => 'client_city_id'])->cache();
	}

	/**
	 * @return ActiveQuery
	 * @deprecated
	 */
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

	public function getEntityResponsible(): EntityResponsibleQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(EntityResponsible::class, ['id' => 'entity_responsible_id']);
	}

	public function getStage(): IssueStageQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(IssueStage::class, ['id' => 'stage_id']);
	}

	public function getType(): ActiveQuery {
		return $this->hasOne(IssueType::class, ['id' => 'type_id']);
	}

	public function getStageType(): ActiveQuery {
		return $this->hasOne(StageType::class, ['type_id' => 'type_id', 'stage_id' => 'stage_id']);
	}

	public function getIssueNotes(): IssueNoteQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(IssueNote::class, ['issue_id' => 'id'])
			->with('user')->orderBy('created_at DESC');
	}

	public function getSummons(): ActiveQuery {
		return $this->hasMany(Summon::class, ['issue_id' => 'id']);
	}

	public function getPayCalculations(): IssuePayCalculationQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(IssuePayCalculation::class, ['issue_id' => 'id']);
	}

	public function getPays(): IssuePayQuery {
		return $this->hasMany(IssuePay::class, ['calculation_id' => 'id'])
			->via('payCalculations');
	}

	public function isArchived(): bool {
		return (int) $this->stage_id === IssueStage::ARCHIVES_ID;
	}

	public function getProvision(): ?Provision {
		if ($this->provision === null) {
			if ($this->isNewRecord) {
				$type = static::DEFAULT_PROVISION;
			} else {
				$type = $this->provision_type;
			}
			if ($type > 0) {
				$this->provision = new Provision($type, [
					'base' => (float) $this->provision_base,
					'value' => (float) $this->provision_value,
				]);
			}
		}
		return $this->provision;
	}

	public function hasTele(): bool {
		return $this->tele !== null;
	}

	public function hasLawyer(): bool {
		return $this->lawyer !== null;
	}

	public function hasProvision(): bool {
		return $this->provision_base > 0;
	}

	public function getClientFullName(): string {
		return trim($this->client_surname) . ' ' . trim($this->client_first_name);
	}

	public function getVictimFullName(): string {
		return trim($this->victim_surname) . ' ' . trim($this->victim_first_name);
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

	public function linkUser(int $userId, string $type): void {
		$user = $this->getUsers()->withType($type)->one();
		if ($user !== null) {
			$user->user_id = $userId;
			$user->save();
		} else {
			$this->link('users', new IssueUser(['user_id' => $userId, 'type' => $type]));
		}
	}

	/**
	 * @param string $type
	 */
	public function unlinkUser(string $type) {
		$user = $this->getUsers()->withType($type)->one();
		if ($user !== null) {
			$this->unlink('users', $user, true);
		}
	}

	/**
	 * @inheritdoc
	 * @return IssueQuery the active query used by this AR class.
	 */
	public static function find(): IssueQuery {
		return new IssueQuery(get_called_class());
	}

	public function isForUser(int $id): bool {
		return $this->getUsers()
			->andWhere(['user_id' => $id])
			->exists();
	}

	public function isForAgents(array $ids): bool {
		return $this->getUsers()
			->withType(IssueUser::TYPE_AGENT)
			->andWhere(['user_id' => $ids])
			->exists();
	}

	public function getIssueModel(): Issue {
		return $this;
	}

	public static function getIssueIdAttribute(): string {
		return 'id';
	}

}

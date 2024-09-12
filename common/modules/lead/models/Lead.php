<?php

namespace common\modules\lead\models;

use common\models\Address;
use common\modules\lead\events\LeadEvent;
use common\modules\lead\models\query\LeadDialerQuery;
use common\modules\lead\models\query\LeadQuery;
use common\modules\lead\Module;
use common\modules\reminder\models\Reminder;
use common\modules\reminder\models\ReminderQuery;
use DateInterval;
use DateTime;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Lead
 *
 * @property int $id
 * @property string $date_at
 * @property string $data
 * @property int $source_id
 * @property int $status_id
 * @property string $name
 * @property string|null $provider
 * @property string|null $phone
 * @property string|null $postal_code
 * @property string|null $email
 * @property int|null $campaign_id
 * @property string|null $deadline_at
 * @property float|null $cost_value
 *
 * @property-read LeadUserInterface|null $owner
 * @property-read LeadCampaign|null $campaign
 * @property-read LeadStatus $status
 * @property-read LeadSource $leadSource
 * @property-read LeadUser[] $leadUsers
 * @property-read LeadReport[] $reports
 * @property-read LeadAnswer[] $answers
 * @property-read LeadAddress[] $addresses
 * @property-read Lead[] $samePhoneLeads
 * @property-read Lead[] $sameEmailLeads
 * @property-read LeadDialer[] $dialers
 * @property-read LeadCost[] $costs
 */
class Lead extends ActiveRecord implements ActiveLead {

	public const DATA_KEY_DETAILS = 'details';

	public const EVENT_AFTER_STATUS_UPDATE = 'afterStatusUpdate';

	public const PROVIDER_COPY = 'copy';
	public const PROVIDER_FORM_LANDING = 'form';
	public const PROVIDER_CZATER = 'czater';
	public const PROVIDER_CENTRAL_PHONE = 'central-phone';
	public const PROVIDER_FORM_ZAPIER = 'form.zapier';

	public const PROVIDER_FORM_WORDPRESS = 'form.wordpress';
	public const PROVIDER_MESSAGE_ZAPIER = 'message.zapier';
	public const PROVIDER_CRM_CUSTOMER = 'crm.customer';

	private ?array $users_ids = null;

	public string $dateFormat = 'Y-m-d H:i:s';

	public function afterSave($insert, $changedAttributes): void {
		parent::afterSave($insert, $changedAttributes);
		$this->linkUsers();
	}

	private function linkUsers(): void {
		if ($this->users_ids !== null && empty($this->users_ids)) {
			$this->unlinkUsers();
		} else {
			$leadUsers = $this->leadUsers;
			$currentTypes = [];
			foreach ($leadUsers as $key => $leadUser) {
				$userId = $this->users_ids[$leadUser->type] ?? null;
				if ($userId === null) {
					$leadUser->delete();
					unset($leadUsers[$key]);
				} else {
					$currentTypes[] = $leadUser->type;
					if ($leadUser->user_id !== $userId) {
						$leadUser->user_id = $userId;
						$leadUser->save();
					}
				}
			}

			foreach ((array) $this->users_ids as $type => $userId) {
				if (!in_array($type, $currentTypes, true)) {
					$this->linkUser($type, $userId);
				}
			}
		}
	}

	public static function tableName(): string {
		return '{{%lead}}';
	}

	public function behaviors(): array {
		return [
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('CURRENT_TIMESTAMP'),
				'attributes' => [
					static::EVENT_BEFORE_UPDATE => 'updated_at',
				],
			],
		];
	}

	public function rules(): array {
		return [
			[['source_id', 'status_id', 'data', 'name'], 'required'],
			[['status_id'], 'integer'],
			[['phone', 'postal_code', 'email', 'provider', 'name'], 'string'],
			[['phone', 'email', 'provider', 'deadline_at'], 'default', 'value' => null],
			['email', 'email'],
			['postal_code', 'string', 'max' => 6],
			['provider', 'in', 'range' => array_keys(static::getProvidersNames())],
			[['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadStatus::class, 'targetAttribute' => ['status_id' => 'id']],
			[['source_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadSource::class, 'targetAttribute' => ['source_id' => 'id']],
			[['campaign_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadCampaign::class, 'targetAttribute' => ['campaign_id' => 'id']],
		];
	}

	public function attributeLabels(): array {
		return [
			'date_at' => Yii::t('lead', 'Date At'),
			'data' => Yii::t('lead', 'Data'),
			'source_id' => Yii::t('lead', 'Source'),
			'source' => Yii::t('lead', 'Source'),
			'status_id' => Yii::t('lead', 'Status'),
			'statusName' => Yii::t('lead', 'Status'),
			'typeName' => Yii::t('lead', 'Type'),
			'sourceName' => Yii::t('lead', 'Source'),
			'name' => Yii::t('lead', 'Lead Name'),
			'provider' => Yii::t('lead', 'Provider'),
			'providerName' => Yii::t('lead', 'Provider'),
			'campaign_id' => Yii::t('lead', 'Campaign'),
			'campaign' => Yii::t('lead', 'Campaign'),
			'phone' => Yii::t('lead', 'Phone'),
			'postal_code' => Yii::t('lead', 'Postal Code'),
			'owner' => Yii::t('lead', 'Owner'),
			'details' => Yii::t('lead', 'Details'),
			'updated_at' => Yii::t('lead', 'Updated At'),
			'customerAddress' => Yii::t('lead', 'Customer Address'),
			'cost_value' => Yii::t('lead', 'Single Costs Value'),
		];
	}

	public function getCustomerAddress(): ?Address {
		return $this->addresses[LeadAddress::TYPE_CUSTOMER]->address ?? null;
	}

	public function getDialers(): LeadDialerQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(LeadDialer::class, ['lead_id' => 'id']);
	}

	//@todo probalby single market for Lead
	public function getMarket() {
		return $this->hasOne(LeadMarket::class, ['lead_id' => 'id']);
	}

	protected function getAddresses(): ActiveQuery {
		return $this->hasMany(LeadAddress::class, ['lead_id' => 'id'])->indexBy('type');
	}

	public function unlinkUsers(): void {
		$this->unlinkAll('leadUsers', true);
	}

	public function linkUser(string $type, int $user_id): void {
		if (!$this->getIsNewRecord()) {
			$this->link('leadUsers', new LeadUser(['type' => $type, 'user_id' => $user_id]));
		}
	}

	public function getCampaign(): ActiveQuery {
		return $this->hasOne(LeadCampaign::class, ['id' => 'campaign_id']);
	}

	public function getLeadSource(): ActiveQuery {
		return $this->hasOne(LeadSource::class, ['id' => 'source_id']);
	}

	public function getStatus(): ActiveQuery {
		return $this->hasOne(LeadStatus::class, ['id' => 'status_id']);
	}

	public function getStatusName(): string {
		return LeadStatus::getNames()[$this->status_id];
	}

	public function getAnswers(): ActiveQuery {
		return $this->hasMany(LeadAnswer::class, ['report_id' => 'id'])->via('reports')
			->indexBy('question_id');
	}

	public function getReminders(): ReminderQuery {
		return $this->hasMany(Reminder::class, ['id' => 'reminder_id'])->viaTable(LeadReminder::tableName(), ['lead_id' => 'id']);
	}

	public function getLeadReminders(): ActiveQuery {
		return $this->hasMany(LeadReminder::class, ['lead_id' => 'id']);
	}

	public function getReports(): ActiveQuery {
		return $this->hasMany(LeadReport::class, ['lead_id' => 'id'])->indexBy('id')->orderBy([LeadReport::tableName() . '.created_at' => SORT_DESC]);
	}

	public function getOwner(): ActiveQuery {
		return $this->hasOne(Module::userClass(), ['id' => 'user_id'])->via('leadUsers', function (ActiveQuery $query) {
			$query->andWhere(['type' => LeadUser::TYPE_OWNER]);
		});
	}

	public function getLeadUsers(): ActiveQuery {
		return $this->hasMany(LeadUser::class, ['lead_id' => 'id']);
	}

	public function getCosts(): ActiveQuery {
		if ($this->isNewRecord) {
			//@todo yii2 not allowed Expression in link attribute.
			$relation = $this->hasMany(LeadCost::class, ['campaign_id' => 'campaign_id']);
			$relation->onCondition([
				LeadCost::tableName() . '.date_at' => Lead::expressionDateAtAsDate(),
			]);
			return $relation;
		}

		return $this->hasMany(LeadCost::class, [
			'campaign_id' => 'campaign_id',
		])->andWhere([LeadCost::tableName() . '.date_at' => date('Y-m-d', strtotime($this->date_at))]);
	}

	public function getDetails(): ?string {
		return $this->getData()[static::DATA_KEY_DETAILS] ?? null;
	}

	public function getId(): int {
		return $this->id;
	}

	public function getHash(): string {
		return Module::manager()->hashLead($this);
	}

	public function getName(): string {
		return $this->name;
	}

	public function getDateTime(): DateTime {
		return new DateTime($this->date_at);
	}

	public function getStatusId(): int {
		return $this->status_id;
	}

	public function getSourceId(): int {
		return $this->source_id;
	}

	public function getData(): array {
		return Json::decode($this->data, true) ?? [];
	}

	public function getPhone(): ?string {
		return $this->phone;
	}

	public function getEmail(): ?string {
		return $this->email;
	}

	public function getPostalCode(): ?string {
		return $this->postal_code;
	}

	public function hasAnswer(int $question_id): bool {
		return isset($this->answers[$question_id]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUsers(): array {
		if (empty($this->users_ids)) {
			$this->users_ids = ArrayHelper::map($this->leadUsers, 'type', 'user_id');
		}
		return $this->users_ids;
	}

	public function getCampaignId(): ?int {
		return $this->campaign_id;
	}

	public function getProvider(): ?string {
		return $this->provider;
	}

	public function getProviderName(): ?string {
		return static::getProvidersNames()[$this->provider] ?? null;
	}

	public function getSource(): LeadSourceInterface {
		return LeadSource::getModels()[$this->source_id];
	}

	public function getTypeId(): int {
		return LeadSource::typeId($this->getSourceId());
	}

	public function getTypeName(): string {
		return LeadType::getNames()[$this->getTypeId()];
	}

	public function getSourceName(): string {
		return LeadSource::getNames()[$this->getSourceId()];
	}

	public function updateFromLead(LeadInterface $lead): void {
		if (!empty($lead->getEmail()) && empty($this->email)) {
			$this->email = $lead->getEmail();
		}
		if (!empty($lead->getPhone()) && empty($this->phone)) {
			$this->phone = $lead->getPhone();
		}
		if (!empty($lead->getPostalCode()) && empty($this->postal_code)) {
			$this->postal_code = $lead->getPostalCode();
		}
		$this->update(true, [
			'email',
			'phone',
			'postal_code',
		]);
	}

	public function getPhoneBlacklist(): ActiveQuery {
		return $this->hasOne(LeadPhoneBlacklist::class, ['phone' => 'phone']);
	}

	public function updateStatus(int $status_id): bool {
		if ($this->status_id !== $status_id) {
			$this->updateAttributes([
				'status_id' => $status_id,
			]);
			$this->trigger(static::EVENT_AFTER_STATUS_UPDATE, new LeadEvent($this));
			return true;
		}
		return false;
	}

	public function updateName(string $name): bool {
		return $this->updateAttributes(['name' => $name]) > 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function findById(string $id): ?self {
		return static::find()
			->andWhere(['id' => $id])
			->one();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function findByLead(LeadInterface $lead): array {
		return static::find()
			->andFilterWhere(['phone' => $lead->getPhone()])
			->orFilterWhere(['email' => $lead->getEmail()])
			->indexBy('id')
			->all();
	}

	public static function getProvidersNames(): array {
		return [
			static::PROVIDER_COPY => Yii::t('lead', 'Copy'),
			static::PROVIDER_FORM_LANDING => Yii::t('lead', 'Form - Landing'),
			static::PROVIDER_FORM_ZAPIER => Yii::t('lead', 'Form - Zapier'),
			static::PROVIDER_FORM_WORDPRESS => Yii::t('lead', 'Form - Wordpress'),
			static::PROVIDER_MESSAGE_ZAPIER => Yii::t('lead', 'Message - Zapier'),
			static::PROVIDER_CZATER => Yii::t('lead', 'Czater'),
			static::PROVIDER_CRM_CUSTOMER => Yii::t('lead', 'CRM - Customer'),
			static::PROVIDER_CENTRAL_PHONE => Yii::t('lead', 'Central phone'),
		];
	}

	public function setLead(LeadInterface $lead): void {
		$this->name = $lead->getName();
		$this->source_id = $lead->getSource()->getID();
		$this->email = $lead->getEmail();
		$this->phone = $lead->getPhone();
		$this->postal_code = $lead->getPostalCode();
		$this->provider = $lead->getProvider();
		$this->date_at = $lead->getDateTime()->format($this->dateFormat);
		$this->data = Json::encode($lead->getData());
		$this->status_id = $lead->getStatusId();
		$this->campaign_id = $lead->getCampaignId();
		$this->users_ids = $lead->getUsers();
	}

	public function isForUser($id): bool {
		foreach ($this->leadUsers as $user) {
			if ($user->user_id === $id) {
				return true;
			}
		}
		return false;
	}

	public function isDelay(): ?bool {
		return $this->getDeadlineHours() > 0;
	}

	public function getDeadlineHours(): ?int {
		$deadline = $this->getDeadline();
		if ($deadline === null) {
			return null;
		}
		$datetime = new DateTime($deadline);
		$diff = $datetime->diff(new DateTime());
		return ($diff->days * 24 + $diff->h) * ($diff->invert ? -1 : 1);
	}

	public function getDeadline(): ?string {
		if (!empty($this->deadline_at)) {
			return $this->deadline_at;
		}
		$hours = $this->status->hours_deadline;
		if (empty($hours)) {
			return null;
		}
		$date = $this->date_at;
		$reports = $this->reports;
		if (!empty($reports)) {
			$date = max(ArrayHelper::getColumn($reports, 'created_at'));
		}
		$datetime = new DateTime($date);
		$datetime->add(new DateInterval("PT{$hours}H"));
		return $datetime->format(DATE_ATOM);
	}

	/**
	 * @return static[]
	 */
	public function getSameContacts(bool $withType = false, bool $refresh = false): array {
		$models = [];
		if (!empty($this->phone)) {
			foreach ($this->samePhoneLeads as $phoneLead) {
				$models[$phoneLead->getId()] = $phoneLead;
			}
		}
		if (!empty($this->email)) {
			foreach ($this->sameEmailLeads as $emailLead) {
				$models[$emailLead->getId()] = $emailLead;
			}
		}
		unset($models[$this->id]);
		if ($withType) {
			return static::typeFilter($models, $this->getTypeId());
		}
		return $models;
	}

	/**
	 * @param static[] $models
	 * @param int $type
	 * @return static[]
	 */
	public static function typeFilter(array $models, int $type): array {
		return array_filter($models, static function (self $model) use ($type): bool {
			return $model->getTypeId() === $type;
		});
	}

	public static function find(): LeadQuery {
		return new LeadQuery(static::class);
	}

	public function getSamePhoneLeads(): LeadQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(static::class, ['phone' => 'phone'])
			->indexBy('id');
	}

	public function getSameEmailLeads(): LeadQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(static::class, ['email' => 'email'])
			->indexBy('id');
	}

	public static function expressionDateAtAsDate(): Expression {
		return new Expression('DATE(' . Lead::tableName() . '.date_at)');
	}
}

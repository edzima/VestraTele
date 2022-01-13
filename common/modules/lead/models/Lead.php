<?php

namespace common\modules\lead\models;

use common\models\Address;
use common\modules\lead\models\query\LeadQuery;
use common\modules\lead\Module;
use common\modules\reminder\models\Reminder;
use DateTime;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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
 *
 * @property-read LeadCampaign|null $campaign
 * @property-read LeadStatus $status
 * @property-read LeadSource $leadSource
 * @property-read LeadUser[] $leadUsers
 * @property-read LeadReport[] $reports
 * @property-read LeadAnswer[] $answers
 * @property-read LeadAddress[] $addresses
 */
class Lead extends ActiveRecord implements ActiveLead {

	public const PROVIDER_FORM = 'form';
	public const PROVIDER_CZATER = 'czater';
	public const PROVIDER_CENTRAL_PHONE = 'central-phone';

	private ?array $users_ids = null;

	public string $dateFormat = 'Y-m-d H:i:s';

	public function afterSave($insert, $changedAttributes): void {
		parent::afterSave($insert, $changedAttributes);
		$this->linkUsers(!$insert);
	}

	private function linkUsers(bool $withUnlink): void {
		if ($withUnlink) {
			$this->unlinkUsers();
		}
		foreach ($this->getUsers() as $type => $id) {
			$this->linkUser($type, $id);
		}
	}

	public static function tableName(): string {
		return '{{%lead}}';
	}

	public function rules(): array {
		return [
			[['source_id', 'status_id', 'data', 'name'], 'required'],
			[['status_id'], 'integer'],
			[['phone', 'postal_code', 'email', 'provider', 'name'], 'string'],
			['phone', 'default', 'value' => null],
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
			'name' => Yii::t('lead', 'Lead Name'),
			'provider' => Yii::t('lead', 'Provider'),
			'providerName' => Yii::t('lead', 'Provider'),
			'campaign_id' => Yii::t('lead', 'Campaign'),
			'campaign' => Yii::t('lead', 'Campaign'),
			'phone' => Yii::t('lead', 'Phone'),
			'postal_code' => Yii::t('lead', 'Postal Code'),
			'owner' => Yii::t('lead', 'Owner'),
		];
	}

	public function getCustomerAddress(): ?Address {
		return $this->addresses[LeadAddress::TYPE_CUSTOMER]->address ?? null;
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

	public function getReminders(): ActiveQuery {
		return $this->hasMany(Reminder::class, ['id' => 'reminder_id'])->viaTable(LeadReminder::tableName(), ['lead_id' => 'id']);
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

	public function getId(): int {
		return $this->id;
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
		return $this->leadSource;
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

	public function updateStatus(int $status_id): bool {
		return $this->updateAttributes(['status_id' => $status_id]) > 0;
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
		//@todo load from Lead Module
		return [
			static::PROVIDER_FORM => Yii::t('lead', 'Form'),
			static::PROVIDER_CZATER => Yii::t('lead', 'Czater'),
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

	public function getSameContacts(): array {
		$models = static::findByLead($this);
		unset($models[$this->id]);
		return $models;
	}

	public static function find(): LeadQuery {
		return new LeadQuery(static::class);
	}
}

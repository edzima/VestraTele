<?php

namespace common\modules\lead\models;

use common\models\Address;
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
			[['source_id', 'status_id', 'data'], 'required'],
			[['status_id'], 'integer'],
			[['phone', 'postal_code', 'email', 'provider'], 'string'],
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
			'source_id' => Yii::t('lead', 'Source'),
			'status_id' => Yii::t('lead', 'Status'),
			'provider' => Yii::t('lead', 'Provider'),
			'phone' => Yii::t('lead', 'Phone'),
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

	public function getAnswers(): ActiveQuery {
		return $this->hasMany(LeadAnswer::class, ['report_id' => 'id'])->via('reports')
			->indexBy('question_id');
	}

	public function getReminders(): ActiveQuery {
		return $this->hasMany(Reminder::class, ['id' => 'reminder_id'])->viaTable(LeadReminder::tableName(), ['lead_id' => 'id']);
	}

	public function getReports(): ActiveQuery {
		return $this->hasMany(LeadReport::class, ['lead_id' => 'id'])->indexBy('id');
	}

	public function getLeadUsers(): ActiveQuery {
		return $this->hasMany(LeadUser::class, ['lead_id' => 'id']);
	}

	public function getId(): string {
		return $this->id;
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
			->andWhere([
				'source_id' => $lead->getSourceId(),
			])
			->andWhere(['or', ['phone' => $lead->getPhone()], ['email' => $lead->getEmail()]])
			->all();
	}

	public static function getProvidersNames(): array {
		return [
			static::PROVIDER_FORM => Yii::t('lead', 'Form'),
			static::PROVIDER_CZATER => Yii::t('lead', 'Czater'),
			static::PROVIDER_CENTRAL_PHONE => Yii::t('lead', 'Central phone'),
		];
	}

	public function setLead(LeadInterface $lead): void {
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
		return in_array($id, $this->getUsers(), true);
	}
}

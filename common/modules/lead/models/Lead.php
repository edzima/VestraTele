<?php

namespace common\modules\lead\models;

use common\modules\lead\Module;
use DateTime;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Class Lead
 *
 * @property int $id
 * @property string $date_at
 * @property string $data
 * @property int $type_id
 * @property int $source_id
 * @property int $status_id
 * @property string|null $phone
 * @property string|null $postal_code
 * @property string|null $email
 * @property int|null $campaign_id
 * @property int|null $owner_id
 *
 * @property-read LeadCampaign|null $campaign
 * @property-read LeadType $type
 * @property-read LeadStatus $status
 * @property-read LeadSource $leadSource
 */
class Lead extends ActiveRecord implements ActiveLead {

	public string $dateFormat = 'Y-m-d H:i:s';

	public static function tableName(): string {
		return '{{%lead}}';
	}

	public function rules(): array {
		return [
			[['source_id', 'status_id', 'data'], 'required'],
			[['status_id', 'owner_id'], 'integer'],
			[['phone', 'postal_code', 'email'], 'string'],
			['email', 'email'],
			['postal_code', 'string', 'max' => 6],
			[['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadStatus::class, 'targetAttribute' => ['status_id' => 'id']],
			[['source_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadSource::class, 'targetAttribute' => ['source_id' => 'id']],
			[['campaign_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadCampaign::class, 'targetAttribute' => ['campaign_id' => 'id']],
			[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::userClass(), 'targetAttribute' => ['owner_id' => 'id']],
		];
	}

	public function getSource(): LeadSourceInterface {
		return $this->leadSource;
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

	public function getReports(): ActiveQuery {
		return $this->hasMany(LeadReport::class, ['lead_id' => 'id']);
	}

	public function getType(): ActiveQuery {
		return $this->hasOne(LeadType::class, ['id' => 'type_id']);
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

	public function getTypeId(): int {
		return $this->type_id;
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

	public function getOwnerId(): ?int {
		return $this->owner_id;
	}

	public function getCampaignId(): ?int {
		return $this->campaign_id;
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

	public function setLead(LeadInterface $lead): void {
		$this->source_id = $lead->getSource()->getID();
		$this->email = $lead->getEmail();
		$this->phone = $lead->getPhone();
		$this->postal_code = $lead->getPostalCode();
		$this->date_at = $lead->getDateTime()->format($this->dateFormat);
		$this->data = Json::encode($lead->getData());
		$this->status_id = $lead->getStatusId();
		$this->campaign_id = $lead->getCampaignId();
	}

}

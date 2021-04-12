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
 * @property int|null $owner_id
 *
 * @property-read LeadType $type
 * @property-read LeadStatus $status
 * @property-read LeadSource $source
 */
class Lead extends ActiveRecord implements ActiveLead {

	public string $dateFormat = 'Y-m-d H:i:s';

	public function rules(): array {
		return [
			[['source_id', 'type_id', 'status_id', 'data'], 'required'],
			[['type_id', 'status_id', 'owner_id'], 'integer'],
			[['phone', 'postal_code', 'email'], 'string'],
			['email', 'email'],
			['postal_code', 'string', 'max' => 6],
			[['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadStatus::class, 'targetAttribute' => ['status_id' => 'id']],
			[['source_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadSource::class, 'targetAttribute' => ['source_id' => 'id']],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadType::class, 'targetAttribute' => ['type_id' => 'id']],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadType::class, 'targetAttribute' => ['type_id' => 'id']],
			[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::userClass(), 'targetAttribute' => ['owner_id' => 'id']],
		];
	}

	public static function tableName(): string {
		return '{{%lead}}';
	}

	public function getId(): string {
		return $this->id;
	}

	public function getSource(): ActiveQuery {
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

	public function getDateTime(): DateTime {
		return new DateTime($this->date_at);
	}

	public function getSourceId(): int {
		return $this->source_id;
	}

	public function getData(): array {
		return Json::decode($this->data, true);
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

	public function getTypeId(): int {
		return $this->type_id;
	}

	public function getStatusId(): int {
		return $this->status_id;
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
				'type_id' => $lead->getTypeId(),
			])
			->andWhere(['or', ['phone' => $lead->getPhone()], ['email' => $lead->getEmail()]])
			->all();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function createFromLead(LeadInterface $lead): self {
		$model = new static();
		$model->source_id = $lead->getSourceId();
		$model->email = $lead->getEmail();
		$model->phone = $lead->getPhone();
		$model->postal_code = $lead->getPostalCode();
		$model->date_at = $lead->getDateTime()->format($model->dateFormat);
		$model->data = Json::encode($lead->getData());
		$model->type_id = $lead->getTypeId();
		$model->status_id = $lead->getStatusId();
		return $model;
	}

}

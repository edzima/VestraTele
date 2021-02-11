<?php

namespace common\modules\lead\models;

use DateTime;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Class Lead
 *
 * @property int $id
 * @property string $date_at
 * @property string $source
 * @property string $data
 * @property int $type_id
 * @property int $status_id
 * @property string|null $phone
 * @property string|null $postal_code
 * @property string|null $email
 * @property int|null $owner_id
 *
 * @property-read LeadType $type
 * @property-read LeadStatus $status
 */
class Lead extends ActiveRecord implements ActiveLead {

	public string $dateFormat = 'Y-m-d H:i:s';

	public static function tableName(): string {
		return '{{%lead}}';
	}

	public function getId(): int {
		return $this->id;
	}

	public function getType(): ActiveQuery {
		return $this->hasOne(LeadType::class, ['id' => 'type_id']);
	}

	public function getStatus(): ActiveQuery {
		return $this->hasOne(LeadStatus::class, ['id' => 'status_id']);
	}

	public function getDateTime(): DateTime {
		return new DateTime($this->date_at);
	}

	public function getSource(): string {
		return $this->source;
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

	public function updateStatus(int $status_id): bool {
		return $this->updateAttributes(['status_id' => $status_id]) > 0;
	}

	public static function findById(int $id): ?self {
		return static::find()->andWhere(['id' => $id])->one();
	}

	public static function findByLead(LeadInterface $lead): ?self {
		return static::find()
			->andWhere(['source' => $lead->getSource()])
			->andWhere(['or', ['phone' => $lead->getPhone()], ['email' => $lead->getEmail()]])
			->one();
	}

	public static function createFromLead(LeadInterface $lead): self {
		$model = new self();
		$model->source = $lead->getSource();
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

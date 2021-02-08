<?php

namespace common\modules\lead\models;

use DateTime;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Class Lead
 *
 * @property int $id
 * @property string $date_at
 * @property string $source
 * @property string $data
 * @property string|null $phone
 * @property string|null $postal_code
 * @property string|null $email
 */
class Lead extends ActiveRecord implements ActiveLead {

	public string $dateFormat = 'Y-m-d H:i:s';

	public static function tableName(): string {
		return '{{%lead}}';
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
		return $model;
	}

}

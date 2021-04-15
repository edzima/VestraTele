<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadSourceInterface;
use common\modules\lead\models\LeadStatus;
use DateTime;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use Yii;
use yii\base\Model;
use yii\helpers\Json;

class LeadForm extends Model implements LeadInterface {

	public $campaign_id;
	public $source_id;
	public $status_id;
	public $type_id;
	public $datetime;
	public $owner_id;
	public $first_name;
	public $lastname;
	public ?string $data = null;
	public ?string $email = null;
	public ?string $phone = null;
	public ?string $postal_code = null;

	public array $owners = [];

	public function rules(): array {
		return [
			[['source_id', 'status_id', 'datetime'], 'required'],
			[
				'phone', 'required', 'enableClientValidation' => false, 'when' => function () {
				return empty($this->email);
			}, 'message' => Yii::t('lead', 'Phone cannot be blank when email is blank.'),
			],
			[
				'email', 'required', 'enableClientValidation' => false, 'when' => function () {
				return empty($this->phone);
			}, 'message' => Yii::t('lead', 'Email cannot be blank when phone is blank.'),
			],
			[['status_id', 'source_id', 'campaign_id'], 'integer'],
			[['phone', 'postal_code', 'email', 'first_name', 'lastname'], 'string'],
			['postal_code', 'string', 'max' => 6],
			['email', 'email'],
			['datetime', 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
			[['phone'], PhoneValidator::class, 'country' => 'PL'],
			['campaign_id', 'in', 'range' => array_keys(static::getCampaignsNames())],
			['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
			['source_id', 'in', 'range' => array_keys(static::getSourcesNames())],
			[
				'owner_id', 'in', 'range' => array_keys($this->owners), 'when' => function (): bool {
				return !empty($this->owners);
			},
			],
		];
	}

	public function attributeLabels(): array {
		return [
			'status_id' => Yii::t('lead', 'Status'),
			'source_id' => Yii::t('lead', 'Source'),
			'datetime' => Yii::t('lead', 'Date At'),
			'phone' => Yii::t('lead', 'Phone'),
			'postal_code' => Yii::t('lead', 'Postal Code'),
			'data' => Yii::t('lead', 'Data'),
		];
	}

	public function setLead(LeadInterface $lead): void {
		$this->setSource($lead->getSource());
		$this->status_id = $lead->getStatusId();
		$this->datetime = $lead->getDateTime()->format(DATE_ATOM);
		$this->data = Json::encode($lead->getData());
		$this->email = $lead->getEmail();
		$this->phone = $lead->getPhone();
		$this->postal_code = $lead->getPostalCode();
	}

	public function setSource(LeadSourceInterface $source): void {
		$this->source_id = $source->getID();
	}

	public function getStatusId(): int {
		return $this->status_id;
	}

	public function getSourceId(): int {
		return $this->source_id;
	}

	public function getDateTime(): DateTime {
		return new DateTime($this->datetime);
	}

	public function getData(): array {
		return Json::decode($this->data) ?? [];
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

	public function getSourceName(): string {
		return static::getSourcesNames()[$this->source_id];
	}

	public function getStatusName(): string {
		return static::getStatusNames()[$this->status_id];
	}

	public static function getCampaignsNames(): array {
		return LeadCampaign::getNames();
	}

	public static function getSourcesNames(): array {
		return LeadSource::getNames();
	}

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

	public function getCampaignId(): ?int {
		return $this->campaign_id;
	}

	public function getSource(): LeadSourceInterface {
		return LeadSource::findOne($this->source_id);
	}

	public function push(): ?ActiveLead {
		if (!$this->validate()) {
			return Yii::$app->leadManager->pushLead($this);
		}
	}

}

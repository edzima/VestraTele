<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadSourceInterface;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadUser;
use common\modules\lead\Module;
use DateTime;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use Yii;
use yii\base\Model;
use yii\helpers\Json;

class LeadForm extends Model implements LeadInterface {

	public const USER_OWNER = LeadUser::TYPE_OWNER;
	public const USER_AGENT = LeadUser::TYPE_AGENT;
	public const USER_TELE = LeadUser::TYPE_TELE;

	public $campaign_id;
	public $source_id;
	public $status_id;
	public $type_id;
	public $datetime;
	public $provider;

	public ?string $data = null;
	public ?string $email = null;
	public ?string $phone = null;
	public ?string $postal_code = null;

	public $owner_id;
	public $agent_id;
	public $tele_id;

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
			[['status_id', 'source_id', 'campaign_id', 'agent_id', 'owner_id', 'tele_id'], 'integer'],
			[['phone', 'postal_code', 'email'], 'string'],
			['postal_code', 'string', 'max' => 6],
			['email', 'email'],
			['datetime', 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
			[['phone'], PhoneValidator::class, 'country' => 'PL'],
			[
				['owner_id', 'agent_id', 'tele_id'], 'in', 'range' => function (): array {
				return array_keys(static::getUsersNames());
			},
			],
			['campaign_id', 'in', 'range' => array_keys(static::getCampaignsNames())],
			['provider', 'in', 'range' => array_keys(static::getProvidersNames())],
			['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
			['source_id', 'in', 'range' => array_keys(static::getSourcesNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'date_at' => Yii::t('lead', 'Date At'),
			'status_id' => Yii::t('lead', 'Status'),
			'source_id' => Yii::t('lead', 'Source'),
			'campaign_id' => Yii::t('lead', 'Campaign'),
			'datetime' => Yii::t('lead', 'Date At'),
			'phone' => Yii::t('lead', 'Phone'),
			'postal_code' => Yii::t('lead', 'Postal Code'),
			'data' => Yii::t('lead', 'Data'),
			'agent_id' => Yii::t('lead', 'Agent'),
			'owner_id' => Yii::t('lead', 'Owner'),
			'tele_id' => Yii::t('lead', 'Tele'),
		];
	}

	public function setLead(LeadInterface $lead): void {
		$this->setSource($lead->getSource());
		$this->setUsers($lead->getUsers());
		$this->status_id = $lead->getStatusId();
		$this->datetime = $lead->getDateTime()->format(DATE_ATOM);
		$this->data = Json::encode($lead->getData());
		$this->email = $lead->getEmail();
		$this->phone = $lead->getPhone();
		$this->postal_code = $lead->getPostalCode();
		$this->provider = $lead->getProvider();
	}

	public function setSource(LeadSourceInterface $source): void {
		$this->source_id = $source->getID();
		$this->owner_id = $source->getOwnerId();
	}

	public function setUsers(array $users): void {
		$this->agent_id = $users[static::USER_AGENT] ?? null;
		$this->owner_id = $users[static::USER_OWNER] ?? null;
		$this->tele_id = $users[static::USER_TELE] ?? null;
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

	public function getCampaignId(): ?int {
		return $this->campaign_id;
	}

	public function getSource(): LeadSourceInterface {
		return LeadSource::findOne($this->source_id);
	}

	public function getProvider(): ?string {
		return $this->provider;
	}

	public function getUsers(): array {
		$users = [];

		$ownerId = $this->getOwnerId();
		if ($ownerId !== null) {
			$users[static::USER_OWNER] = $ownerId;
		}

		if (!empty($this->agent_id)) {
			$users[static::USER_AGENT] = $this->agent_id;
		}

		if (!empty($this->tele_id)) {
			$users[static::USER_TELE] = $this->tele_id;
		}
		return $users;
	}

	private function getOwnerId(): ?int {
		if (empty($this->owner_id)) {
			$this->owner_id = $this->getSource()->getOwnerId();
		}
		return $this->owner_id;
	}

	private function getSourceOwnerId(): ?int {
		return $this->getSource()->getOwnerId();
	}

	public function push(bool $validate = true): ?ActiveLead {
		if ($validate && !$this->validate()) {
			return null;
		}
		$lead = Yii::$app->leadManager->pushLead($this);
		if ($lead) {
			$lead->unlinkUsers();
			$this->linkUsers($lead);
		}
		return $lead;
	}

	public function linkUsers(ActiveLead $lead): void {
		if (!empty($this->agent_id)) {
			$lead->linkUser(static::USER_AGENT, $this->agent_id);
		}
		if (!empty($this->owner_id)) {
			$lead->linkUser(static::USER_OWNER, $this->owner_id);
		}
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

	public static function getProvidersNames(): array {
		return Lead::getProvidersNames();
	}

	public static function getUsersNames(): array {
		return Module::userNames();
	}

	public function isForUser($id): bool {
		return in_array($id, $this->getUsers(), true);
	}
}

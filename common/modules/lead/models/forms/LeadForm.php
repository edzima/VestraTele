<?php

namespace common\modules\lead\models\forms;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\helpers\ArrayHelper;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadSourceInterface;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadUser;
use common\modules\lead\Module;
use DateTime;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\behaviors\AttributeTypecastBehavior;
use yii\helpers\Json;

class LeadForm extends Model implements LeadInterface {

	public const USER_OWNER = LeadUser::TYPE_OWNER;
	public const USER_AGENT = LeadUser::TYPE_AGENT;

	public const SCENARIO_OWNER = 'owner';

	public $campaign_id;
	public $source_id;
	public $status_id;
	public $date_at;
	public $provider;
	public $name;

	public ?int $typeId = null;

	public ?string $data = null;
	public ?string $email = null;
	public ?string $phone = null;
	public ?string $postal_code = null;

	public ?string $details = null;

	public ?string $phoneRegion = null;

	public $owner_id;
	public $agent_id;

	public string $dateFormat = 'Y-m-d H:i:s';

	public array $contactAttributes = [
		'phone',
		'name',
		'email',
	];
	private array $users = [];

	public function init(): void {
		parent::init();
		if ($this->phoneRegion === null) {
			$this->phoneRegion = Yii::$app->formatter->defaultPhoneRegion;
		}
	}

	public function behaviors(): array {
		return [
			'typecast' => [
				'class' => AttributeTypecastBehavior::class,
			],
		];
	}

	public function rules(): array {
		return [
			[['source_id', 'status_id', 'date_at', 'name'], 'required'],
			['!owner_id', 'required', 'on' => static::SCENARIO_OWNER],
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
			[['status_id', 'source_id', 'campaign_id', 'agent_id', 'owner_id'], 'integer'],
			[['phone', 'postal_code', 'email', 'data', 'details'], 'string'],
			[['phone', 'postal_code', 'email', 'data', 'details'], 'trim'],

			[['campaign_id', 'email', 'phone'], 'default', 'value' => null],
			['postal_code', 'string', 'max' => 6],
			['email', 'email'],
			['date_at', 'date', 'format' => 'php:' . $this->dateFormat],
			[
				['owner_id', 'agent_id'], 'in', 'range' => function (): array {
				return array_keys(static::getUsersNames());
			},
			],
			[
				['phone'], PhoneInputValidator::class,
				'default_region' => $this->phoneRegion,
				'region' => Yii::$app->params['phoneInput.preferredCountries'],
			],
			['campaign_id', 'in', 'range' => array_keys($this->getCampaignsNames())],
			['provider', 'in', 'range' => array_keys(static::getProvidersNames())],
			['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
			['source_id', 'in', 'range' => array_keys($this->getSourcesNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'date_at' => Yii::t('lead', 'Date At'),
			'status_id' => Yii::t('lead', 'Status'),
			'source_id' => Yii::t('lead', 'Source'),
			'campaign_id' => Yii::t('lead', 'Campaign'),
			'phone' => Yii::t('lead', 'Phone'),
			'postal_code' => Yii::t('lead', 'Postal Code'),
			'provider' => Yii::t('lead', 'Provider'),
			'data' => Yii::t('lead', 'Data'),
			'agent_id' => Yii::t('lead', 'Agent'),
			'owner_id' => Yii::t('lead', 'Owner'),
			'name' => Yii::t('lead', 'Lead Name'),
			'details' => Yii::t('lead', 'Details'),
		];
	}

	public function setLead(LeadInterface $lead): void {
		$this->setSource($lead->getSource());
		$this->setUsers($lead->getUsers());
		$this->setData($lead->getData());
		$this->name = $lead->getName();
		$this->status_id = $lead->getStatusId();
		$this->date_at = $lead->getDateTime()->format($this->dateFormat);
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
		$this->users = $users;
		$this->agent_id = $users[static::USER_AGENT] ?? null;
		$this->owner_id = $users[static::USER_OWNER] ?? null;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getStatusId(): int {
		return $this->status_id;
	}

	public function getSourceId(): int {
		return $this->source_id;
	}

	public function getDateTime(): DateTime {
		return new DateTime($this->date_at);
	}

	public function getData(): array {
		$data = Json::decode($this->data) ?? [];
		if (empty($this->details)) {
			unset($data[Lead::DATA_KEY_DETAILS]);
		} else {
			$data[Lead::DATA_KEY_DETAILS] = $this->details;
		}
		return $data;
	}

	public function getDetails(): ?string {
		return $this->details;
	}

	public function getPhone(): ?string {
		if ($this->phone) {
			return Yii::$app->formatter->asPhoneDatabase($this->phone, [
				'default_region' => $this->phoneRegion,
			]);
		}
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
		$users = $this->users;

		$ownerId = $this->getOwnerId();
		if ($ownerId !== null) {
			$users[static::USER_OWNER] = $ownerId;
		} else {
			unset($users[static::USER_OWNER]);
		}

		if (!empty($this->agent_id)) {
			$users[static::USER_AGENT] = $this->agent_id;
		} else {
			unset($users[static::USER_AGENT]);
		}

		return $users;
	}

	private function getOwnerId(): ?int {
		if (empty($this->owner_id)) {
			$this->owner_id = $this->getSource()->getOwnerId();
		}
		return $this->owner_id;
	}

	public function getCampaignsNames(): array {
		if ($this->scenario === static::SCENARIO_OWNER) {
			if (!is_int($this->owner_id)) {
				throw new InvalidConfigException('Owner must be integer.');
			}
			$names = LeadCampaign::getNames($this->owner_id);
			if (!empty($this->campaign_id)
				&& !isset($names[$this->campaign_id])
				&& isset(LeadCampaign::getNames()[$this->campaign_id])) {
				$names[$this->campaign_id] = LeadCampaign::getNames()[$this->campaign_id];
			}
			return LeadCampaign::getNames($this->owner_id);
		}
		return LeadCampaign::getNames();
	}

	/**
	 * @throws InvalidConfigException
	 */
	public function getSourcesNames(): array {
		if ($this->scenario === static::SCENARIO_OWNER) {
			if (!is_int($this->owner_id)) {
				throw new InvalidConfigException('Owner must be integer.');
			}
			$names = LeadSource::getNames($this->owner_id, true, $this->typeId, true);
			$this->ensureCurrentSourceName($names);
			return $names;
		}
		$names = LeadSource::getNames(null, true, $this->typeId, true);
		$this->ensureCurrentSourceName($names);
		return $names;
	}

	private function ensureCurrentSourceName(array &$names): void {
		if (!empty($this->source_id) && !isset($names[$this->source_id])) {
			$name = LeadSource::getNames()[$this->source_id] ?? null;
			if ($name !== null) {
				$names[$this->source_id] = $name;
			}
		}
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

	public function getSameContacts(bool $withType = false): array {
		return Lead::findByLead($this);
	}

	private function setData(array $data): void {
		$this->data = Json::encode($data);
		$this->details = ArrayHelper::getValue($data, 'details');
	}

	public function updateLead(Lead $lead, int $updater_id, bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$lead->setLead($this);
		$contactChangedAttributes = $this->getContactChangedAttributes($lead);
		if (!empty($contactChangedAttributes)) {
			$this->createLeadReportAboutContactChanged($lead, $updater_id, $contactChangedAttributes);
		}
		return $lead->update(false);
	}

	public function getContactChangedAttributes(Lead $lead): array {
		$attributes = [];
		foreach ($this->contactAttributes as $attribute) {
			if ($lead->isAttributeChanged($attribute)) {
				$attributes[] = $attribute;
			}
		}
		return $attributes;
	}

	protected function createLeadReportAboutContactChanged(Lead $lead, int $updater_id, array $changedAttributes): bool {
		$report = new LeadReport();
		$report->lead_id = $lead->getId();
		$report->owner_id = $updater_id;
		$report->old_status_id = $lead->getOldAttribute('status_id');
		$report->status_id = $lead->status_id;
		$details[] = Yii::t('lead', 'Updated Contact Attributes!');
		foreach ($changedAttributes as $attribute) {
			$details[] = Yii::t('lead', '{attribute} is changed from: {oldValue} to {newValue}.', [
				'oldValue' => $lead->getOldAttribute($attribute),
				'newValue' => $lead->getAttribute($attribute),
				'attribute' => $lead->getAttributeLabel($attribute),
			]);
		}

		$report->details = implode("\n", $details);

		return $report->save();
	}
}

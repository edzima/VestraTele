<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\components\message\MessageTemplateKeyHelper;
use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\models\issue\IssueUser;
use Yii;
use yii\base\InvalidConfigException;
use yii\mail\MessageInterface;

class IssueMessagesForm extends MessageModel {

	public string $smsClass = IssueSmsForm::class;

	public bool $sendSmsToCustomer = false;
	public bool $sendSmsToAgent = false;
	public bool $sendEmailToCustomer = false;
	public bool $sendEmailToWorkers = false;

	public $workersTypes = [];

	public ?int $sms_owner_id = null;

	protected ?IssueInterface $issue = null;

	public function setIssue(IssueInterface $issue): void {
		$this->issue = $issue;
		if (!$this->issue->getIssueModel()->isNewRecord) {
			$this->workersTypes = array_keys($this->getWorkersUsersTypesNames());
			$this->sendSmsToCustomer = $this->customerHasPhone();
			$this->sendSmsToAgent = $this->agentHasPhones();
			$this->sendEmailToCustomer = $this->customerHasEmail();
			$this->sendEmailToWorkers = !empty($this->getIssueUsersEmails());
		}
	}

	public function rules(): array {
		return [
			[['sendSmsToCustomer', 'sendEmailToCustomer', 'sendSmsToAgent', 'sendEmailToWorkers'], 'boolean'],
			[
				'workersTypes', 'required',
				'enableClientValidation' => false,
				'when' => function (): bool {
					return $this->sendEmailToWorkers && !empty($this->getWorkersUsersTypesNames());
				},
			],
			['workersTypes', 'in', 'range' => array_keys($this->getWorkersUsersTypesNames()), 'allowArray' => true],
			[
				'!sms_owner_id', 'required', 'when' => function (): bool {
				return $this->sendSmsToCustomer || $this->sendSmsToAgent;
			},
				'message' => Yii::t('issue', 'SMS Owner cannot be blank when want send SMS.'),
			],
		];
	}

	public function attributeLabels(): array {
		return [
			'sendSmsToCustomer' => Yii::t('issue', 'Send SMS to Customer'),
			'sendSmsToAgent' => Yii::t('issue', 'Send SMS to Agent'),
			'sendEmailToCustomer' => Yii::t('issue', 'Send Email To Customer'),
			'sendEmailToWorkers' => Yii::t('issue', 'Send Email To Workers'),
			'workersTypes' => Yii::t('issue', 'Workers'),
		];
	}

	public function pushMessages(): ?int {
		if (!$this->validate()) {
			Yii::error($this->getErrors());
			return false;
		}
		return $this->pushCustomerMessages() + $this->pushWorkersMessages();
	}

	public function pushCustomerMessages(): int {
		$count = 0;
		if ($this->sendSmsToCustomer) {
			$sms = $this->getSmsToCustomer();
			if ($sms && $sms->pushJob()) {
				$count++;
			}
		}
		if ($this->sendEmailToCustomer) {
			$message = $this->getEmailToCustomer();
			if ($message && $message->send()) {
				$count++;
			}
		}
		return $count;
	}

	public function pushWorkersMessages(): int {
		$count = 0;
		if ($this->sendSmsToAgent) {
			$sms = $this->getSmsToAgent();
			if ($sms && $sms->pushJob()) {
				$count++;
			}
		}
		if ($this->sendEmailToWorkers) {
			$message = $this->getEmailToWorkers();
			if ($message && $message->send()) {
				$count++;
			}
		}
		return $count;
	}

	public function getSmsToCustomer(): ?IssueSmsForm {
		if (!$this->customerHasPhone()
			|| ($template = $this->getSmsTemplate($this->getCustomerTemplateKey())) === null) {
			return null;
		}
		$this->parseTemplate($template);
		return $this->createCustomerSms($template);
	}

	protected function parseTemplate(MessageTemplate $template): void {
		$this->parseAgent($template);
		$this->parseCustomer($template);
		$this->parseIssue($template);
	}

	public function getSmsToAgent(): ?IssueSmsForm {
		if (!$this->agentHasPhones()
			|| ($template = $this->getSmsTemplate($this->getWorkersTemplateKey())) === null) {
			return null;
		}
		$this->parseTemplate($template);
		return $this->createAgentSms($template);
	}

	public function getEmailToCustomer(): ?MessageInterface {
		if (!$this->customerHasEmail()
			|| ($template = $this->getEmailTemplate($this->getCustomerTemplateKey())) === null) {
			return null;
		}
		$this->parseTemplate($template);
		return $this->createEmail($template)->setTo($this->getCustomerEmail());
	}

	public function getEmailToWorkers(): ?MessageInterface {
		$emails = $this->getIssueUsersEmails();
		if (empty($emails)) {
			return null;
		}
		$template = $this->getEmailTemplate($this->getWorkersTemplateKey());
		if ($template === null) {
			return null;
		}
		$this->parseTemplate($template);
		return $this->createEmail($template)->setTo($emails);
	}

	public function getCustomerTemplateKey(): string {
		return static::keyCustomer($this->keysParts());
	}

	public function getWorkersTemplateKey(): string {
		return static::keyWorkers($this->keysParts());
	}

	public function getWorkersUsersTypesNames(): array {
		$types = [];
		$workers = IssueUser::TYPES_WORKERS;
		if ($this->issue->getIssueModel()->isNewRecord) {
			foreach ($workers as $workerType) {
				$types[$workerType] = IssueUser::getTypesNames()[$workerType];
			}
			return $types;
		}
		foreach ($this->issue->getIssueModel()->users as $issueUser) {
			if (in_array($issueUser->type, $workers, true)) {
				$types[$issueUser->type] = $issueUser->getTypeWithUser();
			}
		}
		return $types;
	}

	public function createCustomerSms(MessageTemplate $template = null): IssueSmsForm {
		return $this->createSms([
			'owner_id' => $this->sms_owner_id,
			'phone' => $this->getCustomerPhone(),
			'userTypes' => [IssueUser::TYPE_CUSTOMER],
		], $template);
	}

	public function createAgentSms(MessageTemplate $template = null): IssueSmsForm {
		return $this->createSms([
			'owner_id' => $this->sms_owner_id,
			'phone' => $this->getAgentPhone(),
			'userTypes' => [IssueUser::TYPE_AGENT],
		], $template);
	}

	/**
	 * @throws InvalidConfigException
	 */
	public function createSms(array $config = [], MessageTemplate $template = null): IssueSmsForm {
		$config['class'] = $this->smsClass;
		/** @var IssueSmsForm $model */
		$model = Yii::createObject($config, [$this->issue]);
		if ($template) {
			$model->message = $template->getSmsMessage();
			$model->note_title = $template->getSubject();
		}
		return $model;
	}

	protected function parseIssue(MessageTemplate $template): void {
		$template->parseSubject(['issue' => $this->issue->getIssueName()]);
		$template->parseBody([
			'issue' => $this->issue->getIssueName(),
			'issueLink' => $this->getIssueFrontendAbsoluteLink(),
		]);
	}

	protected function getIssueFrontendAbsoluteLink(): string {
		$url = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['issue/view', 'id' => $this->issue->getIssueId()]);
		return Html::a($this->issue->getIssueName(), $url);
	}

	protected function parseCustomer(MessageTemplate $template): void {
		$template->parseBody([
			'customerName' => $this->getCustomerName(),
			'customerPhone' => $this->getCustomerPhone(),
			'customerEmail' => $this->getCustomerName(),
		]);
	}

	protected function parseAgent(MessageTemplate $template): void {
		$template->parseBody([
			'agentName' => $this->getAgentName(),
			'agentPhone' => $this->getAgentPhone(),
			'agentEmail' => $this->getAgentEmail(),
		]);
	}

	protected function agentHasPhones(): bool {
		return !empty($this->getAgentPhone());
	}

	protected function customerHasPhone(): bool {
		return !empty($this->getCustomerPhone());
	}

	protected function customerHasEmail(): bool {
		return !empty($this->getCustomerEmail());
	}

	protected function getAgentName(): string {
		return $this->issue->getIssueModel()->agent->getFullName();
	}

	protected function getAgentEmail(): ?string {
		return $this->issue->getIssueModel()->agent->email;
	}

	protected function getAgentPhone(): ?string {
		return $this->issue->getIssueModel()->agent->getPhone();
	}

	protected function getCustomerEmail(): ?string {
		return $this->issue->getIssueModel()->customer->email;
	}

	protected function getCustomerName(): string {
		return $this->issue->getIssueModel()->customer->getFullName();
	}

	protected function getCustomerPhone(): ?string {
		return $this->issue->getIssueModel()->customer->getPhone();
	}

	protected function getIssueUsersEmails(): array {
		return $this->issue
			->getIssueModel()
			->getUsers()
			->withTypes($this->workersTypes)
			->joinWith('user')
			->select('user.email')
			->column();
	}

	protected function getTemplate(string $type, string $baseKey): ?MessageTemplate {
		return $this
			->getTemplateManager()
			->getIssueTypeTemplatesLikeKey(
				static::generateKey($type, $baseKey),
				$this->issue->getIssueType()->id,
				$this->language
			);
	}

	public static function generateKey(string $type, string $key, ?array $issueTypesIds = null): string {
		$parts = array_merge((array) $type, static::mainKeys(), (array) $key);
		if (!empty($issueTypesIds)) {
			$parts[] = MessageTemplateKeyHelper::issueTypesKeyPart($issueTypesIds);
		}
		return MessageTemplateKeyHelper::generateKey($parts);
	}

	protected static function mainKeys(): array {
		return [
			'issue',
		];
	}

	public static function keyCustomer(array $parts = []): string {
		array_unshift($parts, 'customer');
		return MessageTemplateKeyHelper::generateKey($parts);
	}

	public static function keyWorkers(array $parts = []): string {
		array_unshift($parts, 'workers');
		return MessageTemplateKeyHelper::generateKey($parts);
	}

	public function keysParts(): array {
		return [];
	}

}

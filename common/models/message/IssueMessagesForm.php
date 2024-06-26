<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\components\message\MessageTemplateKeyHelper;
use common\helpers\ArrayHelper;
use common\helpers\Html;
use common\models\forms\HiddenFieldsModel;
use common\models\issue\IssueInterface;
use common\models\issue\IssueUser;
use common\models\user\User;
use frontend\helpers\Url;
use Yii;
use yii\base\InvalidConfigException;
use yii\mail\MessageInterface;

class IssueMessagesForm extends MessageModel implements HiddenFieldsModel {

	protected const KEY_ISSUE_TYPES = 'issueTypes';

	protected const KEY_CUSTOMER = 'customer';
	protected const KEY_WORKERS = 'workers';

	public string $smsClass = IssueSmsForm::class;

	public ?bool $sendSmsToCustomer = null;
	public ?bool $sendSmsToAgent = null;
	public ?bool $sendEmailToCustomer = null;
	public ?bool $sendEmailToWorkers = null;

	public $extraWorkersEmails = [];
	private array $_extraWorkersEmailsData = [];

	public array $hiddenFields = [];

	public $workersTypes = [
		IssueUser::TYPE_AGENT,
		IssueUser::TYPE_TELEMARKETER,
	];

	public ?int $sms_owner_id = null;

	public bool $bindIssueType = false;

	public bool $pushMessageEnable = true;

	public bool $excludedIssueUsersInExtraWorkers = false;

	public array $excludedExtraWorkersRoles = [
		User::ROLE_ADMINISTRATOR,
	];
	public array $excludedExtraWorkersIds = [];

	protected ?IssueInterface $issue = null;

	protected ?MessageTemplate $customerSMSTemplate = null;
	protected ?MessageTemplate $agentSMSTemplate = null;
	protected ?MessageTemplate $customerEmailTemplate = null;
	protected ?MessageTemplate $workersEmailTemplate = null;

	public function setIssue(IssueInterface $issue): void {
		$this->issue = $issue;
		if (!$this->issue->getIssueModel()->isNewRecord) {
			if ($this->sendSmsToCustomer === null) {
				$this->sendSmsToCustomer = $this->customerHasPhone();
			}
			if ($this->sendSmsToAgent === null) {
				$this->sendSmsToAgent = $this->agentHasPhones();
			}
			if ($this->sendEmailToCustomer === null) {
				$this->sendEmailToCustomer = $this->customerHasEmail();
			}
			if ($this->sendEmailToWorkers === null) {
				$this->sendEmailToWorkers = !empty($this->getIssueUsersEmails());
			}
			if (!empty($this->workersTypes)) {
				$this->workersTypes = $this->filterIssueWorkersTypes(
					$this->workersTypes,
					ArrayHelper::getColumn($this->issue->getIssueModel()->users, 'type')
				);
			}
		}
	}

	protected function filterIssueWorkersTypes(array $workersTypes, array $issueWorkersTypes): array {
		$defaultWorkersTypes = [];
		foreach ($workersTypes as $type) {
			if (in_array($type, $issueWorkersTypes)) {
				$defaultWorkersTypes[] = $type;
			}
		}
		return $defaultWorkersTypes;
	}

	public function rules(): array {
		return [
			[['sendSmsToCustomer', 'sendEmailToCustomer', 'sendSmsToAgent', 'sendEmailToWorkers'], 'boolean'],
			['workersTypes', 'in', 'range' => array_keys($this->getWorkersUsersTypesNames()), 'allowArray' => true],
			['extraWorkersEmails', 'in', 'range' => array_keys($this->getExtraWorkersEmailsData()), 'allowArray' => true],
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
			'workersTypes' => Yii::t('issue', 'Issue Workers Types'),
			'extraWorkersEmails' => Yii::t('issue', 'Extra Workers Emails'),
		];
	}

	public function addExtraWorkersEmailsIds(array $ids, bool $select = true, bool $excluded = true, string $prefix = null) {
		if ($excluded) {
			$ids = array_diff($ids, $this->excludedExtraWorkersIds());
		}
		$users = User::find()
			->andWhere(['id' => $ids])
			->joinWith('userProfile')
			->active()
			->all();
		foreach ($users as $user) {
			$this->addExtraWorkerEmail($user, $prefix, $select);
		}
	}

	public function addExtraWorkerEmail(User $user, string $prefix = null, bool $select = true): void {
		if ($user->email) {
			$name = $prefix ? $prefix . ' - ' . $user->getFullName() : $user->getFullName();
			$this->_extraWorkersEmailsData[$user->email] = $name;
			if ($select) {
				$this->extraWorkersEmails[] = $user->email;
			}
		}
	}

	public function getExtraWorkersEmailsData(): array {
		if (empty($this->_extraWorkersEmailsData)) {
			if (!empty($this->extraWorkersEmails)) {
				$this->_extraWorkersEmailsData = ArrayHelper::map(User::find()
					->joinWith('userProfile')
					->andWhere(['email' => $this->extraWorkersEmails])
					->andFilterWhere(['<>', 'id', $this->excludedExtraWorkersIds()])
					->active()
					->asArray()
					->all(),
					'email',
					'fullName'
				);
			}
		}
		return $this->_extraWorkersEmailsData;
	}

	protected function excludedExtraWorkersIds(): array {
		if (empty($this->excludedExtraWorkersIds)) {
			if (!empty($this->excludedExtraWorkersRoles)) {
				$this->excludedExtraWorkersIds = User::getAssignmentIds($this->excludedExtraWorkersRoles);
			}
			if ($this->excludedIssueUsersInExtraWorkers) {
				foreach ($this->issue->getIssueModel()->users as $user) {
					$this->excludedExtraWorkersIds[] = $user->user_id;
				}
			}
		}
		return $this->excludedExtraWorkersIds;
	}

	public function pushMessages(): ?int {
		if (!$this->pushMessageEnable) {
			return null;
		}
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
				Yii::warning('push sms to customer: ' . $sms->message, __METHOD__);
				$count++;
			}
		}
		if ($this->sendEmailToCustomer) {
			$message = $this->getEmailToCustomer();
			if ($message && $message->send()) {
				Yii::warning([
					'event' => 'push Email to Customer',
					'bcc' => $message->getTo(),
					'subject' => $message->getSubject(),
					'issue' => $this->issue->getIssueName(),
					'class' => static::class,
				], __METHOD__);

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
				Yii::warning('push sms to agent: ' . $sms->message, __METHOD__);

				$count++;
			}
		}
		if ($this->sendEmailToWorkers) {
			$message = $this->getEmailToWorkers();
			if ($message && $message->send()) {
				Yii::warning([
					'event' => 'push emails to workers',
					'bcc' => $message->getBcc(),
					'subject' => $message->getSubject(),
					'issue' => $this->issue->getIssueName(),
					'class' => static::class,
				], __METHOD__);

				$count++;
			}
		}
		return $count;
	}

	public function getSmsToCustomer(): ?IssueSmsForm {
		if (!$this->customerHasPhone()) {
			return null;
		}
		$template = $this->getCustomerSMSTemplate();
		if ($template === null) {
			return null;
		}
		$this->parseTemplate($template);
		return $this->createCustomerSms($template);
	}

	protected function getCustomerSMSTemplate(): ?MessageTemplate {
		if ($this->customerSMSTemplate === null) {
			$this->customerSMSTemplate = $this->getSmsTemplate($this->getCustomerTemplateKey());
		}
		return $this->customerSMSTemplate;
	}

	public function setCustomerSMSTemplate(MessageTemplate $messageTemplate): void {
		$this->customerSMSTemplate = $messageTemplate;
	}

	public function hasSmsCustomerTemplate(bool $withIssueType = true): bool {
		return $withIssueType
			? $this->getTemplate(static::TYPE_SMS, $this->getCustomerTemplateKey()) !== null
			: parent::getTemplate(static::TYPE_SMS, $this->getCustomerTemplateKey()) !== null;
	}

	protected function parseTemplate(MessageTemplate $template): void {
		$this->parseAgent($template);
		$this->parseCustomer($template);
		$this->parseIssue($template);
		$this->parsePrimaryButton($template);
	}

	public function getSmsToAgent(): ?IssueSmsForm {
		if (!$this->agentHasPhones()) {
			return null;
		}
		$template = $this->getAgentSMSTemplate();
		if ($template === null) {
			return null;
		}
		$this->parseTemplate($template);
		return $this->createAgentSms($template);
	}

	protected function getAgentSMSTemplate(): ?MessageTemplate {
		if ($this->agentSMSTemplate === null) {
			$this->agentSMSTemplate = $this->getSmsTemplate($this->getWorkersTemplateKey());
		}
		return $this->agentSMSTemplate;
	}

	public function setAgentSMSTemplate(MessageTemplate $messageTemplate): void {
		$this->agentSMSTemplate = $messageTemplate;
	}

	public function getEmailToCustomer(): ?MessageInterface {
		if (!$this->customerHasEmail()) {
			return null;
		}
		$template = $this->getCustomerEmailTemplate();
		if ($template === null) {
			return null;
		}
		$this->parseTemplate($template);
		return $this->createEmail($template)->setTo($this->getCustomerEmail());
	}

	protected function getCustomerEmailTemplate(): ?MessageTemplate {
		if ($this->customerEmailTemplate === null) {
			$this->customerEmailTemplate = $this->getEmailTemplate($this->getCustomerTemplateKey());
		}
		return $this->customerEmailTemplate;
	}

	public function setCustomerEmailTemplate(MessageTemplate $messageTemplate): void {
		$this->customerEmailTemplate = $messageTemplate;
	}

	public function getEmailToWorkers(): ?MessageInterface {
		$emails = $this->getWorkersEmails();
		if (empty($emails)) {
			return null;
		}
		$template = $this->getWorkersTemplate();
		if ($template === null) {
			return null;
		}
		$this->parseTemplate($template);
		return $this->createEmail($template)
			->setBcc($emails);
	}

	protected function getWorkersEmails(): array {
		$issue = $this->getIssueUsersEmails();
		$extra = empty($this->extraWorkersEmails) ? [] : (array) $this->extraWorkersEmails;
		if (empty($issue)) {
			if (empty($extra)) {
				return [];
			}
			return $extra;
		}
		if (empty($extra)) {
			return $issue;
		}
		return array_unique(
			array_merge(
				$this->getIssueUsersEmails(),
				$this->extraWorkersEmails
			)
		);
	}

	protected function getWorkersTemplate(): ?MessageTemplate {
		if ($this->workersEmailTemplate === null) {
			$this->workersEmailTemplate = $this->getEmailTemplate($this->getWorkersTemplateKey());
		}
		return $this->workersEmailTemplate;
	}

	public function setWorkersEmailTemplate(MessageTemplate $messageTemplate): void {
		$this->workersEmailTemplate = $messageTemplate;
	}

	public function getCustomerTemplateKey(): string {
		return static::keyCustomer($this->keysParts(static::KEY_CUSTOMER));
	}

	public function getWorkersTemplateKey(): string {
		return static::keyWorkers($this->keysParts(static::KEY_WORKERS));
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
		if ($this->bindIssueType) {
			$template->parseBody(['issueType' => $this->issue->getIssueType()->name]);
		}
	}

	public function getIssueFrontendAbsoluteLink(): string {
		$url = $this->getIssueUrl();
		return Html::a($this->issue->getIssueName(), $url);
	}

	public function getIssueUrl(): string {
		return Url::issueView($this->issue->getIssueId(), true);
	}

	protected function parseCustomer(MessageTemplate $template): void {
		$template->parseBody([
			'customerName' => $this->getCustomerName(),
			'customerPhone' => $this->getCustomerPhone(),
			'customerEmail' => $this->getCustomerEmail(),
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
		$types = (array) $this->workersTypes;
		if (empty($types)) {
			return [];
		}
		return $this->issue
			->getIssueModel()
			->getUsers()
			->withTypes($types)
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
			$parts[] = static::issueTypesKeyPart($issueTypesIds);
		}
		return MessageTemplateKeyHelper::generateKey($parts);
	}

	protected static function mainKeys(): array {
		return [
			'issue',
		];
	}

	public static function keyCustomer(array $parts = []): string {
		array_unshift($parts, static::KEY_CUSTOMER);
		return MessageTemplateKeyHelper::generateKey($parts);
	}

	public static function keyWorkers(array $parts = []): string {
		array_unshift($parts, static::KEY_WORKERS);
		return MessageTemplateKeyHelper::generateKey($parts);
	}

	public function keysParts(string $type): array {
		return [];
	}

	public static function isForIssueType(string $key, int $id): bool {
		$ids = (array) static::getTypesIds($key);
		return empty($ids)
			|| in_array($id, $ids);
	}

	public static function issueTypesKeyPart(array $ids): string {
		if (empty($ids)) {
			return '';
		}
		return MessageTemplateKeyHelper::generateKey([static::KEY_ISSUE_TYPES => $ids]);
	}

	protected static function getTypesIds(string $key) {
		return MessageTemplateKeyHelper::getValue($key, static::KEY_ISSUE_TYPES);
	}

	public function isVisibleField(string $attribute): bool {
		return empty($this->hiddenFields) || !in_array($attribute, $this->hiddenFields, true);
	}

	protected function parsePrimaryButton(MessageTemplate $template): void {
		$template->primaryButtonText = $this->issue->getIssueName();
		$template->primaryButtonHref = $this->getIssueUrl();
	}
}

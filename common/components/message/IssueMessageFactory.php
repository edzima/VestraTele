<?php

namespace common\components\message;

use common\models\issue\IssueInterface;
use common\models\issue\IssueSmsForm;
use common\models\issue\IssueUser;
use Yii;
use yii\base\InvalidConfigException;
use yii\mail\MessageInterface;

class IssueMessageFactory extends MessageFactory {

	public static function keyAboutCreateIssueToCustomer(): string {
		return MessageTemplateKeyHelper::generateKey([
			'issue',
			'create',
			'customer',
		]);
	}

	public static function keyAboutCreateIssueToAgent(): string {
		return MessageTemplateKeyHelper::generateKey([
			'issue',
			'create',
			'agent',
		]);
	}

	public IssueInterface $issue;
	public string $smsClass = IssueSmsForm::class;

	public function getSmsAboutCreateIssueToCustomer(int $creator_id): ?IssueSmsForm {
		if (!$this->customerHasPhones()
			|| ($template = $this->getSmsTemplate(static::keyAboutCreateIssueToCustomer())) === null) {
			return null;
		}
		$template->parseBody([
			'agentName' => $this->getAgentName(),
			'agentPhone' => $this->getAgentPhone(),
		]);
		return $this->createCustomerSms($creator_id, $template);
	}

	public function getEmailAboutCreateIssueToAgent(): ?MessageInterface {
		if (!$this->agentHasEmail()
			|| ($template = $this->getEmailTemplate(static::keyAboutCreateIssueToAgent())) === null) {
			return null;
		}
		$template->parseBody([
			'customerName' => $this->getCustomerName(),
			'customerPhone' => $this->getCustomerPhone(),
		]);
		return $this->createEmail($template);
	}

	public function customerHasPhones(): bool {
		return $this->issue->getIssueModel()->customer->profile->hasPhones();
	}

	public function customerHasEmail(): bool {
		return !empty($this->getCustomerEmail());
	}

	public function agentHasEmail(): bool {
		return !empty($this->getAgentEmail());
	}

	public function agentHasPhones(): bool {
		return $this->issue->getIssueModel()->agent->profile->hasPhones();
	}

	public function getAgentName(): string {
		return $this->issue->getIssueModel()->agent->getFullName();
	}

	public function getAgentEmail(): ?string {
		return $this->issue->getIssueModel()->agent->email;
	}

	public function getAgentPhone(): ?string {
		return $this->issue->getIssueModel()->agent->getPhone();
	}

	public function getCustomerEmail(): ?string {
		return $this->issue->getIssueModel()->customer->email;
	}

	public function getCustomerName(): string {
		return $this->issue->getIssueModel()->customer->getFullName();
	}

	public function getCustomerPhone(): ?string {
		return $this->issue->getIssueModel()->customer->getPhone();
	}

	public function getIssueUsersEmails(array $types): array {
		return $this->issue
			->getIssueModel()
			->getUsers()
			->withTypes($types)
			->joinWith('user')
			->select('user.email')
			->column();
	}

	public function createCustomerSms(int $owner_id, MessageTemplate $template = null): IssueSmsForm {
		return $this->createSms([
			'owner_id' => $owner_id,
			'phone' => $this->getCustomerPhone(),
			'userTypes' => [IssueUser::TYPE_CUSTOMER],
		], $template);
	}

	public function createAgentSms(int $owner_id, MessageTemplate $template = null): IssueSmsForm {
		return $this->createSms([
			'owner_id' => $owner_id,
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
			$model->message = $template->getBody();
			$model->note_title = $template->getSubject();
		}
		return $model;
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
		$parts = [$type, $key];
		if (!empty($issueTypesIds)) {
			$parts[] = MessageTemplateKeyHelper::issueTypesKeyPart($issueTypesIds);
		}
		return MessageTemplateKeyHelper::generateKey($parts);
	}

}

<?php

namespace common\components\message;

use common\helpers\Html;
use common\models\issue\IssuePayInterface;
use common\models\issue\IssueSettlement;
use common\models\issue\IssueSmsForm;
use common\models\issue\IssueUser;
use Yii;
use yii\mail\MessageInterface;

class IssueSettlementMessageFactory extends IssueMessageFactory {

	public static function keyAboutCreateSettlementToCustomer(int $type): string {
		return MessageTemplateKeyHelper::generateKey([
			'issue',
			'settlement',
			'create',
			'customer',
			static::settlementTypeKeyPart($type),
		]);
	}

	public static function keyAboutCreateSettlementToWorkers(int $type): string {
		return MessageTemplateKeyHelper::generateKey([
			'issue',
			'settlement',
			'create',
			'workers',
			static::settlementTypeKeyPart($type),
		]);
	}

	public static function keyAboutPayPaidToCustomer(): string {
		return MessageTemplateKeyHelper::generateKey([
			'issue',
			'settlement',
			'pay',
			'paid',
			'customer',
		]);
	}

	public static function keyAboutPayPaidToWorkers(): string {
		return MessageTemplateKeyHelper::generateKey([
			'issue',
			'settlement',
			'pay',
			'paid',
			'workers',
		]);
	}

	public function init() {
		parent::init();
		$this->fromNameTemplate = '{appName} ' . Yii::t('settlement', 'Settlements');
	}

	public function getEmailAboutCreateSettlementToCustomer(IssueSettlement $settlement): ?MessageInterface {
		$this->issue = $settlement;
		if (!$this->customerHasEmail()) {
			return null;
		}
		$template = $this->getEmailTemplate(static::keyAboutCreateSettlementToCustomer($settlement->getType()));
		if ($template === null) {
			return null;
		}

		$template->parseBody([
			'agentName' => $this->getAgentName(),
			'agentPhone' => $this->getAgentPhone(),
			'agentEmail' => $this->getAgentEmail(),
		]);
		$message = $this->createEmail($template);
		$message->setTo($this->getCustomerEmail());
		return $message;
	}

	public function getEmailAboutCreateSettlementToWorkers(IssueSettlement $settlement, array $userTypes): ?MessageInterface {
		$this->issue = $settlement;
		if (empty($userTypes)) {
			$userTypes = IssueUser::TYPES_WORKERS;
		}
		$emails = $this->getIssueUsersEmails($userTypes);
		if (empty($emails)) {
			return null;
		}
		$template = $this->getEmailTemplate(static::keyAboutCreateSettlementToWorkers($settlement->getType()));
		if ($template === null) {
			return null;
		}
		$template->parseSubject([
			'issue' => $this->issue->getIssueName(),
		]);
		$template->parseBody([
			'customerName' => $this->getCustomerName(),
			'customerPhone' => $this->getCustomerPhone(),
			'customerEmail' => $this->getCustomerEmail(),
			'settlementLink' => Html::a($settlement->getTypeName(), $settlement->getFrontendUrl()),
		]);
		return $this->createEmail($template)->setTo($emails);
	}

	public function getSmsAboutCreateSettlementToCustomer(IssueSettlement $settlement): ?IssueSmsForm {
		$this->issue = $settlement;
		if (!$this->customerHasPhones()) {
			return null;
		}
		$template = $this->getSmsTemplate(static::keyAboutCreateSettlementToCustomer($settlement->getType()));
		if ($template === null) {
			return null;
		}
		$template->parseBody([
			'agentName' => $this->getAgentName(),
			'agentPhone' => $this->getAgentPhone(),
		]);
		return $this->createCustomerSms($settlement->getOwnerId(), $template);
	}

	public function getSmsAboutCreateSettlementToAgent(IssueSettlement $settlement): ?IssueSmsForm {
		$this->issue = $settlement;
		if (!$this->agentHasPhones()) {
			return null;
		}
		$template = $this->getSmsTemplate(static::keyAboutCreateSettlementToWorkers($settlement->getType()));
		if ($template === null) {
			return null;
		}
		$template->parseBody([
			'customerName' => $this->getCustomerName(),
			'customerPhone' => $this->getCustomerPhone(),
		]);
		return $this->createAgentSms($settlement->getOwnerId(), $template);
	}

	public function getSmsAboutPayedPayToCustomer(IssuePayInterface $issuePay, int $user_id): ?IssueSmsForm {
		if (!$issuePay->isPayed() || !$this->customerHasPhones()) {
			return null;
		}
		$template = $this->getSmsTemplate(static::keyAboutPayPaidToCustomer());
		if ($template === null) {
			return null;
		}
		return $this->createCustomerSms($user_id, $template);
	}

	public function getEmailAboutPayedPayToCustomer(IssuePayInterface $issuePay): ?MessageInterface {
		$this->issue = $issuePay->calculation;
		if (!$issuePay->isPayed() || !$this->customerHasEmail()) {
			return null;
		}
		$template = $this->getEmailTemplate(static::keyAboutPayPaidToCustomer());
		if ($template === null) {
			return null;
		}
		$template->parseBody([
			'payValue' => Yii::$app->formatter->asCurrency($issuePay->getValue()),
			'agentName' => $this->getAgentName(),
			'agentEmail' => $this->getAgentEmail(),
			'agentPhone' => $this->getAgentPhone(),
		]);
		return $this->createEmail($template)
			->setTo($this->getCustomerEmail());
	}

	public function getEmailAboutPayedPayToWorkers(IssuePayInterface $issuePay, array $userTypes): ?MessageInterface {
		if (!$issuePay->isPayed()) {
			return null;
		}

		$settlement = $issuePay->calculation;
		$this->issue = $settlement;
		if (empty($userTypes)) {
			$userTypes = IssueUser::TYPES_WORKERS;
		}
		$emails = $this->getIssueUsersEmails($userTypes);
		if (empty($emails)) {
			return null;
		}
		$template = $this->getEmailTemplate(static::keyAboutPayPaidToWorkers());
		if ($template === null) {
			return null;
		}
		$template->parseSubject([
			'issue' => $this->issue->getIssueName(),
		]);
		$template->parseBody([
			'payValue' => Yii::$app->formatter->asCurrency($issuePay->getValue()),
			'customerName' => $this->getCustomerName(),
			'customerPhone' => $this->getCustomerPhone(),
			'customerEmail' => $this->getCustomerEmail(),
			'settlementLink' => Html::a($settlement->getTypeName(), $settlement->getFrontendUrl()),
		]);
		return $this->createEmail($template)->setTo($emails);
	}

	public static function generateKey(string $type, string $key, ?array $issueTypesIds = null, ?int $settlementType = null): string {
		$parts = [$type, $key];
		if ($settlementType !== null) {
			$parts[] = static::settlementTypeKeyPart($settlementType);
		}
		if (!empty($issueTypesIds)) {
			$parts[] = MessageTemplateKeyHelper::issueTypesKeyPart($issueTypesIds);
		}
		return MessageTemplateKeyHelper::generateKey($parts);
	}

	private static function settlementTypeKeyPart(int $type): string {
		return "type:$type";
	}

}

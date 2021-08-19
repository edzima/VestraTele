<?php

namespace backend\modules\settlement\models;

use backend\helpers\Html;
use common\helpers\EmailTemplateKeyHelper;
use common\models\issue\IssueUser;
use common\models\settlement\CalculationForm as BaseCalculationForm;
use Yii;

class CalculationForm extends BaseCalculationForm {

	public bool $sendEmailToCustomer = true;
	public bool $sendEmailToWorkers = true;

	public function rules(): array {
		return array_merge(
			parent::rules(), [
			[
				['sendEmailToWorkers', 'sendEmailToCustomer'], 'boolean',
			],
		]);
	}

	public function attributeLabels(): array {
		return array_merge(
			parent::attributeLabels(), [
			'sendEmailToCustomer' => Yii::t('settlement', 'Send Email to Customer'),
			'sendEmailToWorkers' => Yii::t('settlement', 'Send Email to Workers'),
		]);
	}

	public function init() {
		parent::init();
		$this->sendEmailToCustomer = !empty($this->getIssue()->customer->email);
	}

	public function sendCreateEmailToCustomer(): bool {
		$model = $this->getModel();
		if (empty($model->getIssueModel()->customer->email)) {
			return false;
		}
		$template = Yii::$app->emailTemplate->getIssueTypeTemplatesLikeKey(
			$this->getCreateEmailKeyToCustomer(),
			$model->getIssueType()->id
		);
		if (!$template) {
			return false;
		}
		$issue = $model->getIssueModel();
		$template->parseBody([
			'agentFullName' => $issue->agent->getFullName(),
			'agentPhone' => $issue->agent->profile->phone,
			'agentEmail' => $issue->agent->email,
		]);
		return Yii::$app
			->mailer
			->compose()
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' ' . Yii::t('settlement', 'Settlements')])
			->setTo($this->getModel()->getIssueModel()->customer->email)
			->setSubject($template->getSubject())
			->setHtmlBody($template->getBody())
			->send();
	}

	public function sendCreateEmailToWorkers(array $types = [IssueUser::TYPE_AGENT, IssueUser::TYPE_TELEMARKETER]): bool {
		$emails = $this->getModel()
			->getIssueModel()
			->getUsers()
			->withTypes($types)
			->joinWith('user')
			->select('user.email')
			->column();
		if (empty($emails)) {
			return false;
		}
		$model = $this->getModel();
		$template = Yii::$app->emailTemplate->getIssueTypeTemplatesLikeKey(
			$this->getCreateEmailKeyToWorker(),
			$model->getIssueType()->id
		);
		if (!$template) {
			return false;
		}
		$issue = $model->getIssueModel();
		$template->parseBody([
			'customerFullName' => $issue->agent->getFullName(),
			'settlementLink' => Html::a($model->getName(), $model->getFrontendUrl()),
		]);

		return Yii::$app
			->mailer
			->compose()
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' ' . Yii::t('settlement', 'Settlements')])
			->setTo($emails)
			->setSubject($template->getSubject())
			->setHtmlBody($template->getBody())
			->send();
	}

	public function getCreateEmailKeyToCustomer(): string {
		return EmailTemplateKeyHelper::generateKey(
			[
				EmailTemplateKeyHelper::SETTLEMENT_CREATE,
				strtolower($this->getModel()->getTypeName()),
				EmailTemplateKeyHelper::CUSTOMER,
			]
		);
	}

	public function getCreateEmailKeyToWorker(): string {
		return EmailTemplateKeyHelper::generateKey(
			[
				EmailTemplateKeyHelper::SETTLEMENT_CREATE,
				strtolower($this->getModel()->getTypeName()),
				EmailTemplateKeyHelper::WORKER,
			]
		);
	}

}

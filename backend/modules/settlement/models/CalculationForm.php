<?php

namespace backend\modules\settlement\models;

use common\models\issue\IssueUser;
use common\models\settlement\CalculationForm as BaseCalculationForm;
use Yii;

class CalculationForm extends BaseCalculationForm {

	public function sendEmailToCustomer(): bool {
		return Yii::$app
			->mailer
			->compose(
				['html' => 'settlements/createSettlement-customer-html', 'text' => 'settlements/createSettlement-customer-text'],
				[
					'customer' => $this->getModel()->getIssueModel()->customer,
					'settlement' => $this->getModel(),
				]
			)
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' ' . Yii::t('settlement', 'Settlements')])
			->setTo($this->getModel()->getIssueModel()->customer->email)
			->setSubject(Yii::t('settlement', 'Create Settlement: {type} in Issue: {issue}.', [
				'type' => $this->getModel()->getTypeName(),
				'issue' => $this->getModel()->getIssueName(),
			]))
			->send();
	}

	public function sendEmailToWorkers(array $types = [IssueUser::TYPE_AGENT, IssueUser::TYPE_TELEMARKETER]): bool {
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
		return Yii::$app
			->mailer
			->compose(
				['html' => 'settlements/createSettlement-worker-html', 'text' => 'settlements/createSettlement-worker-text'],
				[
					'settlement' => $this->getModel(),
				]
			)
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' ' . Yii::t('settlement', 'Settlements')])
			->setTo($emails)
			->setSubject(Yii::t('settlement', 'Create Settlement: {type} in Issue: {issue}.', [
				'type' => $this->getModel()->getTypeName(),
				'issue' => $this->getModel()->getIssueName(),
			]))
			->send();
	}
}

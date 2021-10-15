<?php

namespace common\modules\issue\widgets;

use common\models\issue\IssueInterface;
use common\models\issue\IssueSmsForm;
use Yii;
use yii\base\Widget;

class IssueSmsFormWidget extends Widget {

	public IssueSmsForm $model;
	public ?string $userTypeName = null;

	public array $formOptions = [
		'id' => 'issue-sms-push-form',
	];

	public function init(): void {
		if (empty($this->view->title)) {
			$this->view->title = static::getViewTitle($this->model->getIssue(), $this->userTypeName);
		}
		parent::init();
	}

	public function run(): string {
		return $this->render('sms-form', [
			'model' => $this->model,
			'formOptions' => $this->formOptions,
		]);
	}

	public static function getViewTitle(IssueInterface $issue, string $userTypeName = null): string {
		if (!empty($userTypeName)) {
			return Yii::t('issue', 'Send SMS for Issue: {issue} - {userType}', [
				'issue' => $issue->getIssueName(),
				'userType' => $userTypeName,
			]);
		}
		return Yii::t('issue', 'Send SMS for Issue: {issue}', [
			'issue' => $issue,
		]);
	}

}

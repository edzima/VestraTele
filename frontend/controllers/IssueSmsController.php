<?php

namespace frontend\controllers;

use common\models\user\Worker;
use common\modules\issue\controllers\SmsController as BaseSmsController;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use frontend\models\IssueSmsForm;

class IssueSmsController extends BaseSmsController {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::PERMISSION_SMS],
					],
				],
			],
		];
	}

	protected function createModel(int $issue_id): IssueSmsForm {
		$issue = IssueSmsForm::findIssue($issue_id);
		if ($issue === null || !Yii::$app->user->canSeeIssue($issue)) {
			throw new NotFoundHttpException();
		}

		return new IssueSmsForm($issue);
	}
}

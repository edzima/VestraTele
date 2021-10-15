<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueSmsForm;
use common\modules\issue\controllers\SmsController as BaseSmsController;
use yii\web\NotFoundHttpException;

class SmsController extends BaseSmsController {

	protected function createModel(int $issue_id): IssueSmsForm {
		$issue = IssueSmsForm::findIssue($issue_id);
		if ($issue === null) {
			throw new NotFoundHttpException();
		}

		return new IssueSmsForm($issue);
	}

}

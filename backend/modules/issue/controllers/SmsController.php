<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueSmsForm;
use backend\modules\issue\models\IssuesMultipleSmsForm;
use common\helpers\Flash;
use common\modules\issue\controllers\SmsController as BaseSmsController;
use Yii;
use yii\web\NotFoundHttpException;

class SmsController extends BaseSmsController {

	public function actionPushMultiple(array $ids = []) {
		if (empty($ids)) {
			$postIds = Yii::$app->request->post('ids');
			if (is_string($postIds)) {
				$postIds = explode(',', $postIds);
			}
			if ($postIds) {
				$ids = $postIds;
			}
		}
		if (empty($ids)) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('backend', 'IDs must be set.')
			);
			return $this->redirect(['issue/index']);
		}
		if (count($ids) === 1) {
			return $this->redirect(['push', 'id' => reset($ids)]);
		}
		$model = new IssuesMultipleSmsForm();
		$model->ids = $ids;
		$model->owner_id = Yii::$app->user->getId();

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$count = count($model->pushJobs());
			if ($count) {
				Flash::add(Flash::TYPE_SUCCESS, Yii::t('backend', 'Add {count} SMS: {message} to Send Queue.', [
					'count' => $count,
					'message' => $model->message,
				]));
			} else {
				Flash::add(Flash::TYPE_WARNING, Yii::t('backend', 'Problem with add SMS to Queue'));
			}
			return $this->redirect(['issue/index']);
		}
		return $this->render('send-multiple', [
			'model' => $model,
		]);
	}

	protected function createModel(int $issue_id): IssueSmsForm {
		$issue = IssueSmsForm::findIssue($issue_id);
		if ($issue === null) {
			throw new NotFoundHttpException();
		}

		return new IssueSmsForm($issue);
	}

}

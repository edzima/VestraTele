<?php

namespace backend\modules\issue\controllers;

use common\helpers\Flash;
use common\models\issue\IssueSmsForm;
use common\models\issue\IssueUser;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SmsController extends Controller {

	/**
	 * @param int $id
	 * @return Response|string
	 * @throws NotFoundHttpException
	 */
	public function actionPush(int $id, string $userType = null) {
		try {
			$model = new IssueSmsForm($id);
		} catch (InvalidConfigException $exception) {
			throw new NotFoundHttpException($exception->getMessage());
		}

		$userTypeName = null;
		if ($userType !== null) {
			$userTypeName = IssueUser::getTypesNames()[$userType] ?? null;
			if ($userTypeName === null) {
				throw new NotFoundHttpException('Invalid User Type.');
			}
			$model->userTypes = [$userType];
		}
		$model->owner_id = Yii::$app->user->getId();
		$phonesCount = count($model->getPhones());
		if ($phonesCount === 0) {
			Flash::add(Flash::TYPE_WARNING, Yii::t('issue', 'Not Found Phones for this Issue: {issue}', [
				'issue' => $model->getIssue()->getIssueName(),
			]));
			return $this->redirect(['issue/view', 'id' => $id]);
		}
		if ($phonesCount === 1) {
			$model->scenario = IssueSmsForm::SCENARIO_SINGLE;
		} else {
			$model->scenario = IssueSmsForm::SCENARIO_MULTIPLE;
		}

		if ($model->load(Yii::$app->request->post()) && !empty($model->pushJobs())) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('issue', 'Success push SMS: {message} to send queue for Issue:{issue}.', [
					'message' => $model->getMessage()->getMessage(),
					'issue' => $model->getIssue()->getIssueName(),
				]));
			return $this->redirect(['issue/view', 'id' => $id]);
		}
		return $this->render('send', [
			'model' => $model,
			'userTypeName' => $userTypeName,
		]);
	}
}

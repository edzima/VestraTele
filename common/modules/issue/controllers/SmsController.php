<?php

namespace common\modules\issue\controllers;

use common\helpers\Flash;
use common\models\issue\IssueSmsForm;
use common\models\issue\IssueUser;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

abstract class SmsController extends Controller {

	/**
	 * @param int $id
	 * @return Response|string
	 * @throws NotFoundHttpException
	 */
	public function actionPush(int $id, string $userType = null) {
		$model = $this->createModel($id);
		$this->beforeLoadModel($model, $userType);

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
			'userTypeName' => $userType ? $this->getUserTypeName($userType) : null,
		]);
	}

	abstract protected function createModel(int $issue_id): IssueSmsForm;

	protected function beforeLoadModel(IssueSmsForm $model, string $userType = null) {
		$model->owner_id = Yii::$app->user->getId();
		if ($userType !== null) {
			$model->userTypes = [$userType];
			$model->setFirstAvailablePhone();
		}
		$phonesCount = count($model->getPhones());
		if ($phonesCount === 0) {
			Flash::add(Flash::TYPE_WARNING, Yii::t('issue', 'Not Found Phones for this Issue: {issue}', [
				'issue' => $model->getIssue()->getIssueName(),
			]));
			return $this->redirect(['issue/view', 'id' => $model->getIssue()->getIssueId()]);
		}
		if ($phonesCount === 1) {
			$model->scenario = IssueSmsForm::SCENARIO_SINGLE;
		} else {
			$model->scenario = IssueSmsForm::SCENARIO_MULTIPLE;
		}
	}

	/**
	 * @param string $userType
	 * @return string
	 * @throws NotFoundHttpException
	 */
	protected function getUserTypeName(string $userType): string {
		$userTypeName = IssueUser::getTypesNames()[$userType] ?? null;
		if ($userTypeName === null) {
			throw new NotFoundHttpException('Invalid User Type.');
		}
		return $userTypeName;
	}
}

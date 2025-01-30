<?php

namespace common\modules\court\controllers;

use common\models\issue\IssueNoteForm;
use common\modules\court\models\Lawsuit;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class NoteController extends Controller {

	public function actionCreateLawsuit(int $lawsuitId, int $issueId) {
		$lawsuit = Lawsuit::findOne($lawsuitId);
		if ($lawsuit === null) {
			throw new NotFoundHttpException();
		}
		$model = IssueNoteForm::createLawsuit($lawsuit);
		$model->user_id = Yii::$app->user->getId();
		$model->issue_id = $issueId;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['lawsuit/view', 'id' => $lawsuitId]);
		}
		return $this->render('create-lawsuit', [
			'model' => $model,
			'lawsuit' => $lawsuit,
		]);
	}
}

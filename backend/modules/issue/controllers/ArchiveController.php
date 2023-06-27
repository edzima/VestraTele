<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueArchive;
use backend\modules\issue\models\search\IssueArchiveSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class ArchiveController extends Controller {

	/**
	 * Lists all IssueArchive models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new IssueArchiveSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionIssues(string $archiveNr = null): string {
		if ($archiveNr === null) {
			$archiveNr = Yii::$app->request->post('expandRowKey');
		}
		if ($archiveNr === null) {
			throw new BadRequestHttpException();
		}

		$dataProvider = new ActiveDataProvider([
			'pagination' => false,
			'query' => IssueArchive::find()
				->andWhere(['archives_nr' => $archiveNr])
				->with([
					'customer.userProfile',
					'tags',
				])
				->orderBy(['stage_change_at' => SORT_DESC]),

		]);

		return $this->renderPartial('issues', [
			'dataProvider' => $dataProvider,
		]);
	}

}

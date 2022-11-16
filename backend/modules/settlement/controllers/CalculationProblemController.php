<?php

namespace backend\modules\settlement\controllers;

use backend\modules\settlement\models\CalculationProblemStatusForm;
use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use common\models\issue\IssuePayCalculation;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CalculationProblemController extends Controller {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'remove' => ['POST'],
				],
			],
		];
	}

	public function actionIndex(): string {
		$searchModel = new IssuePayCalculationSearch();
		$searchModel->onlyWithProblems = true;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * @param int $id
	 * @return string|Response
	 * @throws NotFoundHttpException
	 */
	public function actionSet(int $id) {
		try {
			$model = new CalculationProblemStatusForm($this->findModel($id));
			Yii::$app->session->addFlash('warning', Yii::t('backend', 'Setting problem status remove all not payed pays.'));

			if ($model->load(Yii::$app->request->post()) && $model->save()) {
				return $this->redirect(['/settlement/calculation/view', 'id' => $id]);
			}
			return $this->render('set', [
				'model' => $model,
			]);
		} catch (InvalidConfigException $exception) {
			Yii::$app->session->addFlash('warning', Yii::t('backend', 'Only not payed calculation can be set problem status.'));
		}
		return $this->redirect(['/settlement/calculation/view', 'id' => $id]);
	}

	public function actionRemove(int $id) {
		$model = $this->findModel($id);
		$model->problem_status = null;
		$model->save(false);
		return $this->redirect(['/settlement/calculation/view', 'id' => $id]);
	}

	/**
	 * Finds the IssuePayCalculation model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssuePayCalculation the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): IssuePayCalculation {
		if (($model = IssuePayCalculation::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

}

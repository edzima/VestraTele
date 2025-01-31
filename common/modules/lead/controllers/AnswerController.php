<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\modules\lead\models\forms\MultipleAnswersForm;
use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\searches\LeadAnswerSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * AnswerController implements the CRUD actions for LeadAnswer model.
 */
class AnswerController extends BaseController {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all LeadAnswer models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new LeadAnswerSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single LeadAnswer model.
	 *
	 * @param integer $report_id
	 * @param integer $question_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $report_id, int $question_id): string {
		return $this->render('view', [
			'model' => $this->findModel($report_id, $question_id),
		]);
	}

	/**
	 * Creates a new LeadAnswer model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new LeadAnswer();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'report_id' => $model->report_id, 'question_id' => $model->question_id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing LeadAnswer model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $report_id
	 * @param integer $question_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $report_id, int $question_id) {
		$model = $this->findModel($report_id, $question_id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'report_id' => $model->report_id, 'question_id' => $model->question_id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionUpdateLead(int $id, int $reportId = null) {
		$lead = $this->findLead($id);
		//@todo must add hash
		$answers = $lead->getAnswers()->andFilterWhere(['report_id' => $reportId])->all();
		if (empty($answers)) {
			Flash::add(
				Flash::TYPE_WARNING,
				Yii::t('lead', 'Not found any Answers.')
			);
			return $this->redirectLead($id);
		}

		$model = new MultipleAnswersForm($answers);

		if ($model->load(Yii::$app->request->post())) {
			Yii::warning('load');
			if ($model->save()) {
				return $this->redirectLead($id);
			}
		} else {
			Yii::warning('not loead');
		}

		return $this->render('update-lead', [
			'model' => $model,
			'lead' => $lead,
		]);
	}

	/**
	 * Deletes an existing LeadAnswer model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $report_id
	 * @param integer $question_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $report_id, int $question_id) {
		$this->findModel($report_id, $question_id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the LeadAnswer model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $report_id
	 * @param integer $question_id
	 * @return LeadAnswer the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $report_id, int $question_id): LeadAnswer {
		if (($model = LeadAnswer::findOne(['report_id' => $report_id, 'question_id' => $question_id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('lead', 'The requested page does not exist.'));
	}
}

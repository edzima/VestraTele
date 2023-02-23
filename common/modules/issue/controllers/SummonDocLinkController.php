<?php

namespace common\modules\issue\controllers;

use common\helpers\Flash;
use common\models\issue\search\SummonDocLinkSearch;
use common\models\issue\SummonDocLink;
use common\models\user\Worker;
use frontend\helpers\Url;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class SummonDocLinkController extends Controller {

	public bool $sendEmailAboutToConfirm = false;

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'done' => ['POST'],
					'not-done' => ['POST'],
					'confirm' => ['POST'],
					'not-confirmed' => ['POST'],
				],
			],
		];
	}

	public function actionToDo(int $parentTypeId = null): string {
		$searchModel = new SummonDocLinkSearch();
		if (!Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
			$searchModel->userId = Yii::$app->user->getId();
		} else {
			if (isset($searchModel->getSummonContractorsNames()[Yii::$app->user->getId()])) {
				$searchModel->summonContractorId = Yii::$app->user->getId();
			}
		}
		$searchModel->status = SummonDocLinkSearch::STATUS_TO_DO;
		$searchModel->issueParentTypeId = $parentTypeId;
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		return $this->render('to-do', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'contractorsNames' => $searchModel->getSummonContractorsNames(),
		]);
	}

	public function actionToConfirm(int $parentTypeId = null): string {
		$searchModel = new SummonDocLinkSearch();
		if (!Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
			$searchModel->userId = Yii::$app->user->getId();
		}
		$searchModel->status = SummonDocLinkSearch::STATUS_TO_CONFIRM;
		$searchModel->issueParentTypeId = $parentTypeId;
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		return $this->render('to-confirm', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionConfirmed(int $parentTypeId = null): string {
		$searchModel = new SummonDocLinkSearch();
		if (!Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
			$searchModel->userId = Yii::$app->user->getId();
		}
		$searchModel->status = SummonDocLinkSearch::STATUS_CONFIRMED;
		$searchModel->issueParentTypeId = $parentTypeId;
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		return $this->render('confirmed', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionDone(int $summon_id, int $doc_type_id, string $returnUrl = null) {
		$model = $this->findModel($summon_id, $doc_type_id);
		$model->done_user_id = Yii::$app->user->id;
		$model->done_at = date(DATE_ATOM);
		if ($model->save()) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('issue', 'Mark Doc: {name} as To Confirm.', [
					'name' => $model->doc->name,
				]));
			$email = $model->summon->owner->getEmail();
			if ($email) {
				Yii::$app
					->mailer
					->compose(
						['html' => 'summonDocToConfirm-html', 'text' => 'summonDocToConfirm-text'],
						[
							'model' => $model,
						]
					)
					->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
					->setTo($email)
					->setSubject(Yii::t('issue', 'In Issue: {issue} has Doc: {name} as To Confirm.', [
						'issue' => $model->summon->getIssueName(),
						'name' => $model->doc->name,
					]))
					->send();
			}
		}
		return $this->redirect($returnUrl ?: Url::previous());
	}

	public function actionNotDone(int $summon_id, int $doc_type_id, string $returnUrl = null) {
		$model = $this->findModel($summon_id, $doc_type_id);
		if ($model->isConfirmed()) {
			throw new ForbiddenHttpException('Only not confirmed docs can unmark as done.');
		}
		$model->done_user_id = null;
		$model->done_at = null;
		$model->save();

		return $this->redirect($returnUrl ?: Url::previous());
	}

	public function actionConfirm(int $summon_id, int $doc_type_id, string $returnUrl = null) {
		$model = $this->findModel($summon_id, $doc_type_id);
		if (!$model->isDone()) {
			$model->done_user_id = Yii::$app->user->getId();
			$model->done_at = date(DATE_ATOM);
		}
		if ($model->summon->isOwner(Yii::$app->user->getId())
			|| Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
			$model->confirmed_user_id = Yii::$app->user->getId();
			$model->confirmed_at = date(DATE_ATOM);
			$model->save();
			return $this->redirect($returnUrl ?: Url::previous());
		}
		throw new ForbiddenHttpException('Only Summon Owner or Manager can confirm Doc.');
	}

	public function actionNotConfirmed(int $summon_id, int $doc_type_id, string $returnUrl = null) {
		$model = $this->findModel($summon_id, $doc_type_id);
		if (!$model->isConfirmed()) {
			throw new NotFoundHttpException();
		}
		if ($model->confirmed_user_id === Yii::$app->user->getId()
			|| Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
			$model->confirmed_user_id = null;
			$model->confirmed_at = null;
			$model->save();
			return $this->redirect($returnUrl ?: Url::previous());
		}
		throw new ForbiddenHttpException('Only Doc Confirm User or Manager can unmark confirm.');
	}

	private function findModel(int $summon_id, int $doc_type_id): SummonDocLink {
		$model = SummonDocLink::find()
			->andWhere([
				'doc_type_id' => $doc_type_id,
				'summon_id' => $summon_id,
			])
			->one();
		if ($model === null) {
			throw new NotFoundHttpException();
		}

		if ($model->summon->isForUser(Yii::$app->user->getId())
			|| Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
			return $model;
		}
		throw new ForbiddenHttpException('Only for Summon User on Summon Manager.');
	}
}

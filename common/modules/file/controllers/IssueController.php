<?php

namespace common\modules\file\controllers;

use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\message\IssueFilesUploadMessagesForm;
use common\models\user\User;
use common\models\user\Worker;
use common\modules\file\helpers\FilePreviewHelper;
use common\modules\file\models\FileAccess;
use common\modules\file\models\IssueFile;
use common\modules\file\models\IssueFileAccess;
use common\modules\file\models\IssueFileOverwrite;
use common\modules\file\models\UploadForm;
use common\modules\file\Module;
use Yii;
use yii\filters\HttpCache;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @property Module $module
 */
class IssueController extends Controller {

	public function behaviors(): array {

		return array_merge(parent::behaviors(),
			[
				'httpCache' => [
					'class' => HttpCache::class,
					'only' => ['download'],
					'etagSeed' => function (): ?string {
						return Yii::$app->request->userAgent;
					},
					'cacheControlHeader' => 'public, max-age=31536000',
					'sessionCacheLimiter' => 'public',
				],
				'verbs' => [
					'class' => VerbFilter::class,
					'actions' => [
						'revoke-access' => ['POST'],
						'delete' => ['POST'],
					],
				],
			]);
	}

	public bool $checkCanSeeIssue = true;

	protected function checkIssueAccess(IssueInterface $issue): void {
		if (Yii::$app->user->can(Worker::ROLE_ISSUE_FILE_MANAGER)) {
			$this->checkCanSeeIssue = false;
		}
		if ($this->checkCanSeeIssue && !Yii::$app->user->canSeeIssue($issue)) {
			throw new ForbiddenHttpException();
		}
	}

	public function actionView(int $issue_id, int $file_id): string {
		$model = $this->findIssueFile($issue_id, $file_id);
		if (!$this->isOwnerOrManager($model)) {
			throw new ForbiddenHttpException('Only File Owner or Issue File manager can access.');
		}

		return $this->render('view', [
			'model' => $model,
		]);
	}

	public function actionAccess(int $issue_id, int $file_id) {
		$issueFile = $this->findIssueFile($issue_id, $file_id);
		if (!$this->isOwnerOrManager($issueFile)) {
			throw new ForbiddenHttpException('Only File Owner or Issue File manager can access.');
		}
		$model = new IssueFileAccess($issueFile);
		if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
			$model->save();
			return $this->redirect(['view', 'issue_id' => $issue_id, 'file_id' => $file_id]);
		}
		return $this->render('access', [
			'model' => $model,
		]);
	}

	public function actionRevokeAccess(int $issue_id, int $file_id, int $user_id) {
		$issueFile = $this->findIssueFile($issue_id, $file_id);
		if (!$this->isOwnerOrManager($issueFile)) {
			throw new ForbiddenHttpException('Only File Owner or Issue File manager can access.');
		}
		FileAccess::deleteAll(['file_id' => $file_id, 'user_id' => $user_id]);
		return $this->redirect(['view', 'issue_id' => $issue_id, 'file_id' => $file_id]);
	}

	private function isOwnerOrManager(IssueFile $model): bool {
		return $model->file->owner_id === Yii::$app->user->getId()
			|| Yii::$app->user->can(Worker::ROLE_ISSUE_FILE_MANAGER);
	}

	public function actionSingleUpload(int $issue_id, int $file_type_id) {
		$fileType = $this->module->findFileType($file_type_id);
		if ($fileType === null) {
			throw new NotFoundHttpException();
		}
		$issue = $this->findIssue($issue_id);
		$this->checkIssueAccess($issue);
		$model = new UploadForm($fileType, $this->module);
		$model->userId = Yii::$app->user->getId();
		$data = [];
		if ($model->saveUploads($issue)) {
			$data['append'] = true;
			$previewHelper = FilePreviewHelper::createForIssue($issue_id);
			$data['initialPreview'] = $previewHelper->getInitialPreview($model->getAttachedFiles());
			$data['initialPreviewConfig'] = $previewHelper->getInitialPreviewConfig($model->getAttachedFiles());
		} else {
			$data['message'] = $model->getFirstErrors();
		}
		return $this->asJson($data);
	}

	public function actionUpload(int $issue_id, int $file_type_id) {
		$fileType = $this->module->findFileType($file_type_id);
		if ($fileType === null) {
			throw new NotFoundHttpException();
		}
		$issue = $this->findIssue($issue_id);
		$this->checkIssueAccess($issue);
		$model = new IssueFilesUploadMessagesForm();
		$model->workersTypes = [
			Worker::ROLE_AUDITOR,
		];
		$model->setIssue($issue);
		$model->setFileType($fileType);
		$model->addExtraWorkersEmailsIds(User::getAssignmentIds([Worker::PERMISSION_MESSAGE_EMAIL_ISSUE_UPLOAD_FILE]));
		if ($model->load(Yii::$app->request->post())) {
			$model->fileUploader = Yii::$app->user->getIdentity()->getFullName();
			$files = $issue->getIssueModel()->getFilesByType($file_type_id);
			if (!empty($files)) {
				$model->setFiles($files);
				$model->pushMessages();
			}
			$model->pushMessages();

			return $this->redirect(['/issue/issue/view', 'id' => $issue_id]);
		}
		return $this->render('upload', [
			'issue' => $issue,
			'model' => $model,
			'type' => $fileType,
			'messages' => $model,
		]);
	}

	public function actionOverwrite(int $issue_id, int $file_id) {
		$issueFile = $this->findIssueFile($issue_id, $file_id);
		if (!$this->isOwnerOrManager($issueFile)) {
			throw new ForbiddenHttpException('Only File Owner or Issue File manager can access.');
		}
		$model = new IssueFileOverwrite($issueFile, $this->module);
		if (Yii::$app->request->isPost && $model->save()) {
			return $this->redirect(['view', 'issue_id' => $issue_id, 'file_id' => $file_id]);
		}
	}

	public function actionDownload(int $issue_id, int $file_id): Response {
		$issueFile = $this->findIssueFile($issue_id, $file_id);
		$this->checkIssueAccess($issueFile->issue);
		$path = $issueFile->file->path;
		Yii::warning('load action');
		if (!$this->module->getFlysystem()->has($path)) {
			Yii::warning('Fly system has not file with ID: ' . $file_id . ' for path: ' . $path);
			//@todo maybe should delete IssueFile and File models.
			//	$issueFile->delete();
			//	$issueFile->file->delete();
			throw new NotFoundHttpException();
		}
		$content = $this->module->getFlysystem()->read($path);
		return Yii::$app->response->sendContentAsFile($content, $issueFile->file->getNameWithType());
	}

	public function actionDelete(int $issue_id, int $file_id) {
		$model = $this->findIssueFile($issue_id, $file_id);
		$this->checkIssueAccess($model->issue);
		if (!$this->canDelete($model)) {
			throw new ForbiddenHttpException();
		}
		if ($this->module->detachFile($model->file) && $model->delete()) {
			if (Yii::$app->request->isAjax) {
				return $this->asJson(true);
			}
		} else {
			if (Yii::$app->request->isAjax) {
				return $this->asJson(false);
			}
		}
		return $this->redirect(['/issue/issue/view', 'id' => $issue_id]);
	}

	protected function findIssue(int $id): IssueInterface {
		$model = Issue::findOne($id);
		if ($model) {
			return $model;
		}
		throw new NotFoundHttpException();
	}

	protected function findIssueFile(int $issueId, int $fileId): IssueFile {
		$issueFile = IssueFile::find()
			->with('issue')
			->with('file')
			->andWhere([
				'issue_id' => $issueId,
				'file_id' => $fileId,
			])
			->one();
		if (empty($issueFile)) {
			throw new NotFoundHttpException('not found issue file');
		}
		if (!$issueFile->isForUser(Yii::$app->user->getId())) {
			throw new ForbiddenHttpException();
		}
		return $issueFile;
	}

	private function canDelete(IssueFile $model): bool {
		if ($model->file->owner_id === Yii::$app->user->getId()) {
			return true;
		}
		return Yii::$app->user->can(User::PERMISSION_ISSUE_FILE_DELETE_NOT_SELF) || Yii::$app->user->can(Worker::ROLE_ISSUE_FILE_MANAGER);
	}
}

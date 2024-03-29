<?php

namespace common\modules\lead\controllers;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\Module;
use Yii;
use yii\base\ActionEvent;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BaseController extends Controller {

	/**
	 * @var Module
	 */
	public $module;

	protected ?bool $allowDelete = null;
	protected string $deleteAction = 'delete';

	public function init() {
		parent::init();
		if ($this->allowDelete === null) {
			$this->allowDelete = $this->module->allowDelete;
		}
		$this->attachBeforeDeleteAction();
	}

	protected function attachBeforeDeleteAction(): void {
		if (!$this->allowDelete) {
			$this->on(static::EVENT_BEFORE_ACTION, function (ActionEvent $actionEvent): void {
				if ($actionEvent->action->id === $this->deleteAction) {
					Yii::warning([
						'message' => Yii::t('lead', 'User {id} try access to delete action', ['id' => Yii::$app->user->getId()]),
						'controller' => $this->id,
					], 'lead.delete');
					throw new MethodNotAllowedHttpException();
				}
			});
		}
	}

	/**
	 * @param int $id
	 * @return ActiveLead|ActiveRecord
	 * @throws NotFoundHttpException
	 */
	protected function findLead(int $id, bool $forUser = true): ActiveLead {
		$model = $this->module->manager->findById($id, false);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		if ($forUser && !$this->module->manager->isForUser($model, Yii::$app->user->getId())) {
			throw new ForbiddenHttpException(Yii::t('lead', 'You have not access to Lead.'));
		}
		return $model;
	}

	protected function validateHash(ActiveLead $lead, string $hash, bool $throwException = true): bool {
		$validate = $this->module->manager->validateLead($lead, $hash);
		if (!$validate) {
			Yii::warning(
				'User: ' . Yii::$app->user->getId() . ' try check Lead: ' . $lead->getId() . ' with invaldiate hash.',
				__METHOD__
			);
			if ($throwException) {
				throw new NotFoundHttpException();
			}
		}
		return $validate;
	}

	protected function redirectLead(int $id): Response {
		return $this->redirect(['lead/view', 'id' => $id]);
	}

}

<?php

namespace common\modules\lead\controllers;

use common\modules\lead\models\forms\ArchiveForm;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;

class ArchiveController extends BaseController {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'self' => ['POST'],
					'same-contact' => ['POST'],
				],
			],
		];
	}

	public function actionSelf(int $id): Response {
		$model = new ArchiveForm();
		$model->setLead($this->findLead($id));
		$model->userId = Yii::$app->user->getId();
		$model->selfChange = true;
		$model->withSameContacts = false;
		$model->save();
		return $this->redirectLead($id);
	}

	public function actionSameContact(int $id, bool $onlySameType): Response {
		$model = new ArchiveForm();
		$model->setLead($this->findLead($id));
		$model->userId = Yii::$app->user->getId();
		$model->selfChange = false;
		$model->withSameContacts = true;
		$model->withSameContactWithType = $onlySameType;
		$model->save();
		return $this->redirectLead($id);
	}

}

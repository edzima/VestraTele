<?php

namespace common\modules\lead\controllers;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\Module;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class BaseController extends Controller {

	/**
	 * @var Module
	 */
	public $module;

	/**
	 * @param int $id
	 * @return ActiveLead|ActiveRecord
	 * @throws NotFoundHttpException
	 */
	protected function findLead(int $id): ActiveLead {
		$model = $this->module->manager->findById($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}

}

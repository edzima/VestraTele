<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\modules\lead\models\LeadCSVImport;
use Yii;
use yii\web\UploadedFile;

class ImportController extends BaseController {

	public function init() {
		@set_time_limit(300) or Yii::warning('Not set time limit');
		parent::init();
	}

	public function actionCsv() {
		$model = new LeadCSVImport();
		if (Yii::$app->request->isPost) {
			$model->csvFile = UploadedFile::getInstance($model, 'csvFile');
		}
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$imported = $model->import(false);
			if ($imported) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('lead', 'Success Import: {count} from CSV.', [
						'count' => $imported,
					]));
			} else {
				Flash::add(Flash::TYPE_ERROR,
					Yii::t('lead', 'Problem with Import Leads from CSV.'));
			}

			return $this->redirect(['lead/index']);
		}

		return $this->render('csv', [
			'model' => $model,
		]);
	}
}

<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\searches\LeadDialerSearch;
use Yii;
use yii\web\NotFoundHttpException;

class DialerLeadController extends BaseController {

	public function actionIndex(int $dialerId = null) {
		$dialer = $this->module->getDialer();
		if ($dialer === null) {
			throw new NotFoundHttpException('Dialer not configured.');
		}
		$model = new LeadDialerSearch($dialer);
		$ids = LeadDialerSearch::getDialersIds();
		if (empty($ids)) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'Not Found Lead with Dialers.')
			);
			return $this->goBack('/lead/lead/index');
		}
		if ($dialerId !== null && !in_array($dialerId, $ids)) {
			throw new NotFoundHttpException('Not Existed Dialer ID.');
		}
		if ($dialerId === null) {
			$dialerId = (int) reset($ids);
		}
		$dialer->userId = $dialerId;

		$model->load(Yii::$app->request->queryParams);

		return $this->render('index', [
			'model' => $model,
		]);
	}

	public function actionDelete(int $id) {
		LeadUser::deleteAll([
			'lead_id' => $id,
			'type' => LeadUser::TYPE_DIALER,
		]);
		return $this->goBack();
	}
}

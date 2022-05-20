<?php

namespace frontend\controllers;

use common\modules\lead\models\forms\CzaterCallLeadForm;
use common\modules\lead\models\forms\CzaterConvLeadForm;
use common\modules\lead\Module;
use Yii;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class LeadCzaterController extends Controller {

	public function actionConvBegin() {
		Yii::warning(Yii::$app->request->post(), 'lead.czater.convBegin');
		$id = Yii::$app->request->post('idConversation');
		if (!$id) {
			Yii::warning('Missing idConversation', 'lead.czater.convBegin');
			throw new BadRequestHttpException(Yii::t('yii', 'Missing required parameters: {params}', [
				'params' => 'idConversation',
			]));
		}
		$model = Yii::$app->czater->getConv($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		$czaterLead = new CzaterConvLeadForm();
		$czaterLead->setConv($model);
		if ($czaterLead->validate()) {
			$lead = $czaterLead->findLead();
			if ($lead === null) {
				$lead = Module::manager()->pushLead($czaterLead);
			}
			if ($lead) {
				return true;
			}
		} else {
			Yii::warning($czaterLead->getErrors(), 'lead.czater.convBegin.errors');
		}
	}

	public function actionConvEnd(): void {
		Yii::warning(Yii::$app->request->post(), 'lead.czater.convEnd');
	}

	public function actionCallEnd() {
		Yii::warning([
			'get' => Yii::$app->request->get(),
			'post' => Yii::$app->request->post(),
			'queryParams' => Yii::$app->request->getQueryParams(),

		], 'lead.czater.callEnd');
		$id = Yii::$app->request->post('idDataset');
		if (!$id) {
			Yii::warning('Missing idDataset', 'lead.czater.callEnd');
			throw new BadRequestHttpException(Yii::t('yii', 'Missing required parameters: {params}', [
				'params' => 'idDataset',
			]));
		}
		$model = Yii::$app->czater->getCall($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		$czaterLead = new CzaterCallLeadForm();
		$czaterLead->setCall($model);
		if ($czaterLead->validate()) {
			$lead = $czaterLead->findLead();
			if ($lead === null) {
				$lead = Module::manager()->pushLead($czaterLead);
			}
			if ($lead) {
				return true;
			}
		} else {
			Yii::warning($czaterLead->getErrors(), 'lead.czater.callEnd.errors');
		}
	}

}

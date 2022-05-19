<?php

namespace common\modules\czater\controllers;

use common\modules\czater\entities\Client;
use yii\web\NotFoundHttpException;

class ClientController extends BaseController {

	public function actionIndex(): string {
		$dataProvider = $this->createDataProvider();
		$dataProvider->models = $this->module->czater->getClients($dataProvider->pagination->getOffset());
		$dataProvider->key = static function (Client $client): int {
			return $client->idClient;
		};

		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionView(int $id): string {
		$model = $this->module->czater->getClient($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}

		return $this->render('view', [
			'model' => $model,
		]);
	}

}

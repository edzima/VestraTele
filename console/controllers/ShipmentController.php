<?php

namespace console\controllers;

use common\models\issue\IssueShipmentPocztaPolska;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class ShipmentController extends Controller {

	public function actionCheckPocztaPolska(): void {
		$models = IssueShipmentPocztaPolska::find()
			->andWhere(['finished_at' => null])
			->all();
		if (empty($models)) {
			Console::output('Not find Models to Check.');
			return;
		}
		Console::output('Find Models to Check: ' . count($models) . '.');
		$count = 0;
		$poczta = Yii::$app->pocztaPolska;

		foreach ($models as $model) {
			/** @var IssueShipmentPocztaPolska $model */
			$poczta->checkShipment($model->shipment_number);
			$shipment = $poczta->getShipment();
			if ($shipment->isOk()) {
				$model->setShipment($shipment);
				$model->save();
				$count++;
			}
		}
		Console::output('Shipment with OK Status: ' . $count . '.');
	}
}

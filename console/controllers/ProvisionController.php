<?php

namespace console\controllers;

use common\components\provision\exception\MissingProvisionUserException;
use common\models\provision\Provision;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class ProvisionController extends Controller {

	public function actionDoubles(): void {
		$models = Provision::find()
			->select('*, COUNT(*)')
			->andWhere('to_user_id = from_user_id')
			->groupBy('pay_id, to_user_id, from_user_id, type_id')
			->having('COUNT(*) > 1')
			->all();

		Console::output(count($models));
		/** @var Provision $model */
		$settlements = [];
		foreach ($models as $model) {
			Console::output($model->getIssueName());
			if (!isset($settlements[$model->pay->calculation_id])) {
				$settlements[$model->pay->calculation_id] = $model->pay->calculation;
			}
		}
		Console::output('Find Settlements with doubles: ' . count($settlements));
		foreach ($settlements as $settlement) {
			Yii::$app->provisions->removeForPays($settlement->getPays()->getIds());
			try {
				Yii::$app->provisions->settlement($settlement);
			} catch (MissingProvisionUserException $exception) {
				Console::output($exception->getMessage());
			}
		}
	}
}

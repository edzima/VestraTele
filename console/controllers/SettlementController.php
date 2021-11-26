<?php

namespace console\controllers;

use backend\modules\settlement\models\AdministrativeCalculationForm;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\message\IssuePayDelayedMessagesForm;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class SettlementController extends Controller {

	public function actionAdministrativeCosts(): void {
		$models = IssuePayCalculation::find()
			->andWhere(['type' => IssuePayCalculation::TYPE_ADMINISTRATIVE])
			->all();

		Console::output('Find administrative settlements: ' . count($models));
		foreach ($models as $model) {
			if ($model->hasCosts) {
				Console::output('Has already costs: ' . $model->getIssueName());
				Console::output('Costs sum with VAT: ' . $model->getCostsSum(true));
			} else {
				Console::output('Settlement: ' . $model->id . ' for Issue: ' . $model->getIssueName() . ' has not costs.');
				$administrative = AdministrativeCalculationForm::createFromModel($model);
				$administrative->save();
				Yii::$app->provisions->removeForPays($model->getPays()->getIds());
			}
		}
	}

}

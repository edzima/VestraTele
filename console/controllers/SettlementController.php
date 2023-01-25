<?php

namespace console\controllers;

use backend\modules\settlement\models\AdministrativeCalculationForm;
use common\components\provision\exception\Exception;
use common\models\issue\IssueCost;
use common\models\issue\IssuePayCalculation;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class SettlementController extends Controller {

	public function actionRemoveOfficeCosts(string $startDate): void {
		$models = IssuePayCalculation::find()
			->andWhere(['type' => IssuePayCalculation::TYPE_ADMINISTRATIVE])
			->joinWith('issue')
			->andWhere(['>', 'issue.created_at', $startDate])
			->all();

		foreach ($models as $model) {
			Console::output($model->getIssueName());
			$hasOfficeCosts = false;
			foreach ($model->costs as $cost) {

				if ($cost->type === IssueCost::TYPE_OFFICE) {
					$cost->delete();
					$hasOfficeCosts = true;
				}
			}
			if ($hasOfficeCosts) {
				$model->refresh();
				Yii::$app->provisions->removeForPays($model->getPays()->getIds());
				try {
					Yii::$app->provisions->settlement($model);
				} catch (Exception $exception) {
					Console::output($exception->getMessage());
				}
			}
		}
	}

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
				Yii::$app->provisions->removeForPays($model->getPays()->getIds(true));
			}
		}
	}

}

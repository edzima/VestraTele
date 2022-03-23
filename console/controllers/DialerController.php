<?php

namespace console\controllers;

use common\modules\lead\models\LeadDialerType;
use yii\console\Controller;
use yii\helpers\Console;

class DialerController extends Controller {

	public function actionActivateTypes(array $ids): void {
		$models = LeadDialerType::find()
			->andWhere(['id' => $ids])
			->all();

		foreach ($models as $model) {
			$this->activeType($model);
		}
	}

	public function actionInactivateTypes(array $ids): void {
		$models = LeadDialerType::find()
			->andWhere(['id' => $ids])
			->all();

		foreach ($models as $model) {
			$this->inactiveType($model);
		}
	}

	public function activeType(LeadDialerType $type): void {
		if ($type->status !== LeadDialerType::STATUS_ACTIVE) {
			$type->status = LeadDialerType::STATUS_ACTIVE;
			if ($type->save()) {
				Console::output('Success Activate Type: ' . $type->name);
			}
		} else {
			Console::output('Type: ' . $type->name . ' is already active.');
		}
	}

	public function inactiveType(LeadDialerType $type): void {
		if ($type->status !== LeadDialerType::STATUS_INACTIVE) {
			$type->status = LeadDialerType::STATUS_INACTIVE;
			if ($type->save()) {
				Console::output('Success Inactive Type: ' . $type->name);
			}
		} else {
			Console::output('Type: ' . $type->name . ' is already inactive.');
		}
	}

}

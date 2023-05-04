<?php

namespace console\controllers;

use common\helpers\ArrayHelper;
use common\models\PotentialClient;
use common\models\user\User;
use yii\console\Controller;
use yii\helpers\Console;

class PotentialClientController extends Controller {

	public function actionContactAgreement() {
		$ids = [];
		$phones = ArrayHelper::map(PotentialClient::find()
			->select(['id', 'phone'])
			->andWhere(['status' => PotentialClient::STATUS_CONTACT])
			->andWhere('phone IS NOT NULL')
			->asArray()
			->all(),
			'id',
			'phone');
		foreach ($phones as $id => $phone) {
			if (User::find()
				->withPhone($phone)
				->exists()
			) {
				$ids[] = $id;
			}
		}
		if (!empty($ids)) {
			$count = PotentialClient::updateAll([
				'status' => PotentialClient::STATUS_AGREEMENT,
			], [
				'id' => $ids,
			]);
			Console::output('Potential Client Contact update to Agreement: ' . $count);
		}
	}
}

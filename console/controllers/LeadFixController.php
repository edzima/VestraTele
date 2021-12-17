<?php

namespace console\controllers;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\query\LeadQuery;
use yii\console\Controller;
use yii\helpers\Console;

class LeadFixController extends Controller {

	public function actionOwnerFromReports(): void {
		$query = Lead::find()
			->joinWith('leadUsers')
			->andWhere(['!=', 'lead_user.type', LeadUser::TYPE_OWNER])
			->andWhere(['NOT IN', 'source_id', [18, 19, 20, 21]]);

		foreach ($query->batch() as $rows) {
			foreach ($rows as $lead) {
				/** @var $lead Lead */
				if (!array_key_exists(LeadUser::TYPE_OWNER, $lead->getUsers())) {
					Console::output($lead->getId());
				}
			}
		}
	}

}

<?php

namespace common\modules\lead\widgets;

use common\modules\lead\models\LeadReport;
use yii\base\Widget;

class LeadReportWidget extends Widget {

	public LeadReport $model;

	public function run(): string {
		return $this->render('report', [
			'model' => $this->model,
		]);
	}

}

<?php

namespace common\modules\meet\widgets;

use common\models\issue\IssueMeet;
use yii\base\Widget;

class MeetDetailView extends Widget {

	/**
	 * @var IssueMeet
	 */
	public $model;

	public function run() {
		return $this->render('meet-detail-view', ['model' => $this->model]);
	}

}

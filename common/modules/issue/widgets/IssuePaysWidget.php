<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-14
 * Time: 22:51
 */

namespace common\modules\issue\widgets;

use common\models\issue\IssuePay;
use yii\base\Widget;

class IssuePaysWidget extends Widget {

	/** @var IssuePay[] */
	public $models;

	public function run() {
		if (!empty($this->models)) {
			return $this->render('issue-pays', [
				'models' => $this->models
			]);
		}
	}
}
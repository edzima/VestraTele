<?php

namespace common\fixtures\settlement;

use common\models\issue\IssuePayCalculation;
use yii\test\ActiveFixture;

class CalculationCostFixture extends ActiveFixture {

	public function init() {
		if (empty($this->tableName)) {
			$this->tableName = IssuePayCalculation::viaCostTableName();
		}
		parent::init();
	}

	public $depends = [
		CostFixture::class,
		CalculationFixture::class,
	];

}

<?php

namespace frontend\tests\unit\models;

use common\tests\_support\UnitModelTrait;
use frontend\models\HintCitySourceForm;
use frontend\tests\unit\Unit;
use yii\base\Model;

class HintCitySourceFormTest extends Unit {

	use UnitModelTrait;

	private HintCitySourceForm $model;

	public function testEmpty(): void {
		$this->giveModel([]);
		$this->thenUnsuccessValidate();
		codecept_debug($this->getModel()->getErrors());
	}

	private function giveModel(array $config): void {
		$this->model = new HintCitySourceForm($config);
	}

	private function giveHint(string $index): void {

	}

	public function getModel(): Model {
		return $this->model;
	}
}

<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\SummonDocForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\SummonDoc;
use common\tests\_support\UnitModelTrait;
use yii\base\Model;

class SummonDocFormTest extends Unit {

	use UnitModelTrait;

	private SummonDocForm $model;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::summon()
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessSave();
		$this->thenSeeError('Name cannot be blank.', 'name');
	}

	public function testSave():void{
		$this->giveModel();
		$this->model->name = 'Test Doc';
		$this->thenSuccessSave();
		$this->thenSeeRecord([
			'name' => 'Test Doc'
		]);
	}


	public function getModel(): Model {
		return $this->model;
	}

	public function giveModel(array $config = []): void {
		$this->model = new SummonDocForm($config);
	}

	private function thenSeeRecord(array $attributes) {
		$this->tester->seeRecord(SummonDoc::class,$attributes);
	}
}

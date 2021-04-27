<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadReportSchemaForm;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;

class LeadReportSchemaFormTest extends Unit {

	use UnitModelTrait;

	private LeadReportSchemaForm $model;

	public function _fixtures(): array {
		return LeadFixtureHelper::schemas();
	}

	public function testEmpty(): void {
		$this->giveForm([]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Name cannot be blank.', 'name');
	}

	public function testOnlyName(): void {
		$this->giveForm([
			'name' => 'Some schema name',
		]);
		$this->thenSuccessSave();
	}

	public function testSingleStatus(): void {
		$this->giveForm([
			'name' => 'Some schema name',
			'status_ids' => '1',
		]);
		$this->thenSuccessSave();
	}

	private function giveForm(array $config): void {
		$this->model = new LeadReportSchemaForm($config);
	}

	public function getModel(): Model {
		return $this->model;
	}
}

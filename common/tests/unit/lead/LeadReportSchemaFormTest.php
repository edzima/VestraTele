<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadReportSchemaForm;
use common\modules\lead\models\LeadReportSchema;
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

	public function testWithStatus(): void {
		$this->giveForm([
			'name' => 'Some schema name',
			'status_id' => '1',
		]);
		$this->thenSuccessSave();
		$this->thenSeeRecord([
			'name' => 'Some schema name',
			'status_id' => '1',
		]);
	}

	public function testWithType(): void {
		$this->giveForm([
			'name' => 'Some schema name',
			'type_id' => '1',
		]);
		$this->thenSuccessSave();
		$this->thenSeeRecord([
			'name' => 'Some schema name',
			'type_id' => '1',
		]);
	}

	private function thenSeeRecord(array $attributes): void {
		$this->tester->seeRecord(LeadReportSchema::class, $attributes);
	}

	private function giveForm(array $config): void {
		$this->model = new LeadReportSchemaForm($config);
	}

	public function getModel(): Model {
		return $this->model;
	}
}

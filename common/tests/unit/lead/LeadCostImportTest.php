<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\import\FBAdsCostImport;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;

class LeadCostImportTest extends Unit {

	use UnitModelTrait;

	private FBAdsCostImport $model;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::cost(),
			LeadFixtureHelper::campaign()
		);
	}

	public function getModel(): Model {
		return $this->model;
	}

	public function testEmpty(): void {
		$this->giveModel([
			'columns' => [],
			'file' => null,
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('CSV File cannot be blank.', 'file');
		$this->thenSeeError('Columns cannot be blank.', 'columns');
	}

	public function testColumnsSortableAsString() {
		$this->giveModel();
		$this->model->columns = [];
		$this->model->sortableColumns = implode(',', [
			FBAdsCostImport::COLUMN_VALUE,
			FBAdsCostImport::COLUMN_DATE,
		]);
		$this->thenSuccessValidate(['columns']);
		$this->thenDontSeeError('columns');
	}

	public function testColumnsWithoutDateAndValue(): void {
		$this->giveModel();
		$this->model->columns = [];
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Columns cannot be blank.', 'columns');
		$this->model->columns = [FBAdsCostImport::COLUMN_AD_NAME => 2];
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Columns value with key: "date" is required.', 'columns');
		$this->thenSeeError('Columns value with key: "value" is required.', 'columns');
		$this->tester->assertContains('Columns value with key: "date" is required.', $this->model->getErrors('columns'));
		$this->tester->assertContains('Columns value with key: "value" is required.', $this->model->getErrors('columns'));
	}

	public function testImportData() {
		$this->giveModel();
		$this->model->file = codecept_data_dir('lead' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR . 'pixel-export.csv');
		$this->tester->assertSame(48, $this->model->import());
	}

	public function testDoubleSameImportData() {
		$this->giveModel();
		$this->model->file = codecept_data_dir('lead' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR . 'pixel-export.csv');
		$this->tester->assertSame(48, $this->model->import());
		$this->tester->assertSame(0, $this->model->import());
	}

	public function testImportWithoutCreateCampaign() {
		$this->giveModel();
		$this->model->file = codecept_data_dir('lead' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR . 'pixel-export.csv');
		$this->model->createCampaigns = false;
		$this->tester->assertSame(0, $this->model->import());
	}

	private function giveModel(array $config = []) {
		$this->model = new FBAdsCostImport($config);
	}
}

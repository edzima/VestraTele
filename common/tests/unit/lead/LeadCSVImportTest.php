<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCSVImport;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\web\UploadedFile;

class LeadCSVImportTest extends Unit {

	use UnitModelTrait;

	private LeadCSVImport $model;
	private UploadedFile $file;

	public function _fixtures(): array {
		return LeadFixtureHelper::leads();
	}

	public function testNotCsv(): void {
		$this->giveFile('empty.xl');
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Only files with these extensions are allowed: csv.', 'csvFile');
	}

	public function testEmptyFile(): void {
		$this->giveFile('empty.csv');
		$this->giveModel();
		$this->model->source_id = 1;
		$this->thenSuccessValidate();
		$this->tester->assertEmpty($this->model->import());
	}

	public function testCentralPhone(): void {
		$this->giveFile('central-phone.csv');
		$this->giveModel();
		$this->model->source_id = 1;
		$this->model->provider = Lead::PROVIDER_CENTRAL_PHONE;
		$this->model->status_id = 2;
		$this->model->dateColumn = 0;
		$this->model->phoneColumn = 1;
		$this->model->nameColumn = null;
		$this->thenSuccessValidate();
		$this->tester->assertSame(4, $this->model->import());
		$this->tester->seeRecord(Lead::class, [
				'name' => 'central-phone.2',
				'source_id' => 1,
				'status_id' => 2,
				'phone' => '+48 695 613 134',
				'provider' => Lead::PROVIDER_CENTRAL_PHONE,
			]
		);
		$this->tester->seeRecord(Lead::class, [
				'name' => 'central-phone.3',
				'source_id' => 1,
				'status_id' => 2,
				'phone' => '+48 607 040 632',
				'provider' => Lead::PROVIDER_CENTRAL_PHONE,
			]
		);
	}

	public function testComma(): void {
		$this->giveFile('comma.csv');
		$this->giveModel();
		$this->model->source_id = 1;
		$this->model->provider = Lead::PROVIDER_CENTRAL_PHONE;
		$this->model->csvDelimiter = LeadCSVImport::DELIMITER_COMMA;
		$this->model->status_id = 2;
		$this->model->dateColumn = null;
		$this->model->phoneColumn = 3;
		$this->model->nameColumn = null;
		$this->tester->assertSame(2, $this->model->import());
	}

	public function testNotUtf(): void {
		$this->giveFile('not-utf.csv');
		$this->giveModel([
			'source_id' => 3,
			'csvDelimiter' => LeadCSVImport::DELIMITER_SEMICOLON,
			'phoneColumn' => 1,
			'dateColumn' => null,
			'nameColumn' => null,
		]);

		$this->assertSame(2, $this->model->import());
	}

	public function testCommaFileWithSemicolonDelimiter(): void {
		$this->giveFile('comma.csv');
		$this->giveModel();
		$this->model->source_id = 1;
		$this->model->provider = Lead::PROVIDER_CENTRAL_PHONE;
		$this->model->csvDelimiter = LeadCSVImport::DELIMITER_SEMICOLON;
		$this->model->status_id = 2;
		$this->model->dateColumn = null;
		$this->model->phoneColumn = 3;
		$this->model->nameColumn = null;
		$this->tester->assertSame(0, $this->model->import());
	}

	public function testCommaFileWithColonDelimiter(): void {
		$this->giveFile('comma.csv');
		$this->giveModel();
		$this->model->source_id = 1;
		$this->model->provider = Lead::PROVIDER_CENTRAL_PHONE;
		$this->model->csvDelimiter = LeadCSVImport::DELIMITER_COLON;
		$this->model->status_id = 2;
		$this->model->dateColumn = null;
		$this->model->phoneColumn = 3;
		$this->model->nameColumn = null;
		$this->tester->assertSame(0, $this->model->import());
	}

	protected function giveFile(string $fileName): void {
		$file = new UploadedFile();
		$file->name = $fileName;
		$file->tempName = codecept_data_dir() . 'lead/csv/' . $fileName;
		$this->file = $file;
	}

	protected function giveModel(array $config = []): void {
		$this->model = new LeadCSVImport($config);
		$this->model->csvFile = $this->file;
	}

	public function getModel(): LeadCSVImport {
		return $this->model;
	}
}

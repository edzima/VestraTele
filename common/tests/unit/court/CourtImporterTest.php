<?php

namespace common\tests\unit\court;

use common\fixtures\court\CourtFixture;
use common\fixtures\helpers\TerytFixtureHelper;
use common\modules\court\components\CourtImporter;
use common\modules\court\models\Court;
use common\tests\unit\Unit;
use Yii;

class CourtImporterTest extends Unit {

	private CourtImporter $importer;

	public function _before() {
		$this->importer = new CourtImporter();
		$this->importer->fileName = codecept_data_dir() . 'court/data.xlsx';
	}

	public function _fixtures(): array {
		return array_merge([
			'court' => [
				'class' => CourtFixture::class,
				'dataFile' => codecept_data_dir() . '/court/court.php',
			],
		], TerytFixtureHelper::fixtures());
	}

	public function _after() {
		ob_flush();
	}

	public function testGetLastUpdatedAtOnEmptyTable() {
		Yii::$app->db->createCommand()->delete($this->importer->tableName)->execute();
		$this->tester->assertNull($this->importer->getLastUpdateAt());
	}

	public function testGetLastUpdatedAtFromFixtures() {
		$date = $this->importer->getLastUpdateAt();
		$this->tester->assertNotNull($date);
		$this->tester->assertSame('2024-02-27', $date);
	}

	public function testImport() {
		Yii::$app->db->createCommand()->delete($this->importer->tableName)->execute();
		$count = $this->importer->import();
		$this->tester->assertSame(377, Court::find()->count());
		$this->tester->assertSame(377, $count);
	}

}

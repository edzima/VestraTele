<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\models\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\models\lawsuit\LawsuitPartyDTO;
use common\modules\court\modules\spi\models\lawsuit\LawsuitViewIntegratorDto;
use yii\data\ArrayDataProvider;

class LawsuitApiTest extends BaseApiTest {

	const TEST_LAWSUIT_ID = 5431301;

	public function testGetSingleLawsuit() {
		$model = $this->api->getLawsuit(static::TEST_LAWSUIT_ID);
		$this->tester->assertInstanceOf(LawsuitDetailsDto::class, $model);
		$this->tester->assertSame('o podział majatku wpólnego', $model->subject);
	}

	public function testGetLawsuits(): void {
		$dataProvider = $this->api->getLawsuits();
		$this->tester->assertInstanceOf(ArrayDataProvider::class, $dataProvider);
		$this->tester->assertCount(2, $dataProvider->getModels());
		$this->tester->assertSame(2, $dataProvider->getTotalCount());
		foreach ($dataProvider->getModels() as $model) {
			$this->tester->assertInstanceOf(LawsuitViewIntegratorDto::class, $model);
			$lawsuitParties = $model->getLawsuitParties();
			$this->tester->assertIsArray($lawsuitParties);
			$this->tester->assertNotEmpty($lawsuitParties);
			foreach ($lawsuitParties as $lawsuitParty) {
				$this->tester->assertInstanceOf(LawsuitPartyDTO::class, $lawsuitParty);
			}
		}
	}
}

<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\models\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\models\lawsuit\LawsuitPartyDTO;
use common\modules\court\modules\spi\models\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\modules\spi\repository\LawsuitRepository;
use yii\data\ArrayDataProvider;

class LawsuitApiTest extends BaseApiTest {

	const TEST_APPEAL = '';
	const TEST_LAWSUIT_ID = 5431301;

	const TEST_SIGNATURE = 'I ACa 35/12';

	public function testGetSingleLawsuit() {
		$repository = new LawsuitRepository($this->api);
		$model = $repository->getLawsuit(static::TEST_LAWSUIT_ID, static::TEST_APPEAL);
		$this->tester->assertInstanceOf(LawsuitDetailsDto::class, $model);
		$this->tester->assertSame('o podział majatku wpólnego', $model->subject);
	}

	public function testFindBySignature() {
		$repository = new LawsuitRepository($this->api);
		$dataProvider = $repository->findBySignature(static::TEST_SIGNATURE, static::TEST_APPEAL);
		$this->tester->assertNotEmpty($dataProvider->getModels());
	}

	public function testGetLawsuits(): void {
		$repository = new LawsuitRepository($this->api);
		$dataProvider = $repository->getLawsuits(static::TEST_APPEAL);
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

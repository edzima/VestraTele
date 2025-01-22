<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\helpers\ApiDataProvider;
use common\modules\court\modules\spi\models\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\models\lawsuit\LawsuitPartyDTO;
use common\modules\court\modules\spi\models\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\modules\spi\repository\LawsuitRepository;

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
		$model = $repository->findBySignature(static::TEST_SIGNATURE, static::TEST_APPEAL);
		$this->tester->assertNotNull($model);
	}

	public function testFindByNotExistedSignature() {
		$repository = new LawsuitRepository($this->api);
		$model = $repository->findBySignature(static::TEST_SIGNATURE . '.sdasdas', static::TEST_APPEAL);
		$this->tester->assertNull($model);
	}

	public function testGetDataProvider(): void {
		$repository = new LawsuitRepository($this->api);
		$dataProvider = $repository->getDataProvider(static::TEST_APPEAL);
		$this->tester->assertInstanceOf(ApiDataProvider::class, $dataProvider);
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

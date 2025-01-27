<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\entity\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitPartyDTO;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\modules\spi\helpers\ApiDataProvider;
use common\modules\court\modules\spi\repository\LawsuitRepository;

class LawsuitRepositoryTest extends BaseRepositoryTest {

	const TEST_LAWSUIT_ID = 5431301;

	const TEST_SIGNATURE = 'I ACa 35/12';

	public function testGetSingleLawsuit() {
		$repository = $this->repository;
		$model = $repository->getLawsuit(static::TEST_LAWSUIT_ID);
		$this->tester->assertInstanceOf(LawsuitDetailsDto::class, $model);
		$this->tester->assertSame('o podział majatku wpólnego', $model->subject);
	}

	public function testFindBySignature() {
		$repository = $this->repository;
		$model = $repository->findBySignature(static::TEST_SIGNATURE);
		$this->tester->assertNotNull($model);
	}

	public function testFindByNotExistedSignature() {
		$repository = $this->repository;
		$model = $repository->findBySignature(static::TEST_SIGNATURE . '.sdasdas');
		$this->tester->assertNull($model);
	}

	public function testGetDataProvider(): void {
		$repository = $this->repository;
		$dataProvider = $repository->getDataProvider();
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

	protected function repositoryClass(): string {
		return LawsuitRepository::class;
	}
}

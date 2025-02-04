<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\entity\lawsuit\LawsuitSessionDTO;
use common\modules\court\modules\spi\repository\CourtSessionsRepository;

/**
 * @property CourtSessionsRepository $repository
 */
class CourtSessionsTest extends BaseRepositoryTest {

	protected function repositoryClass(): string {
		return CourtSessionsRepository::class;
	}

	public function testLawsuit() {
		$id = LawsuitRepositoryTest::TEST_LAWSUIT_ID;

		$repository = $this->repository;
		$dataProvider = $repository->getByLawsuit($id);
		$this->tester->assertNotEmpty($dataProvider->getModels());
		foreach ($dataProvider->getModels() as $model) {
			$this->tester->assertInstanceOf(LawsuitSessionDTO::class, $model);
		}
	}
}

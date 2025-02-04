<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\entity\lawsuit\LawsuitProceedingDTO;
use common\modules\court\modules\spi\repository\ProceedingsRepository;

/**
 * @property ProceedingsRepository $repository
 */
class ProceedingsRepositoryTest extends BaseRepositoryTest {

	protected function repositoryClass(): string {
		return ProceedingsRepository::class;
	}

	public function testByLawsuit(): void {
		$repository = $this->repository;
		$dataProvider = $repository->getByLawsuit(LawsuitRepositoryTest::TEST_LAWSUIT_ID);
		$dataProvider->setPagination(false);

		$models = $dataProvider->getModels();
		foreach ($models as $model) {
			$this->tester->assertInstanceOf(LawsuitProceedingDTO::class, $model);
		}
	}
}

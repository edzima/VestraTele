<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\repository\BaseRepository;
use Yii;

class BaseRepositoryTest extends BaseApiTest {

	protected BaseRepository $repository;
	public $repositoryModelClass;

	public array $dataProviderSearchParams = [];

	protected function repositoryConfig(): array {
		return [
			'class' => $this->repositoryModelClass,
		];
	}

	public function _before(): void {
		parent::_before();
		$this->repository = Yii::createObject($this->repositoryConfig(), [$this->api]);
		$this->repository->setAppeal(static::TEST_APPEAL);
	}

	public function testGetDataProvider(): void {
		$dataProvider = $this->repository->getDataProvider($this->dataProviderSearchParams);
		$models = $dataProvider->getModels();
		$this->tester->assertIsArray($models);
		$this->tester->assertIsInt($dataProvider->getTotalCount());
	}
}

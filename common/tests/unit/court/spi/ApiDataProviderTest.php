<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\modules\spi\helpers\ApiDataProvider;

class ApiDataProviderTest extends BaseApiTest {

	public function testGetModels(): void {
		$dataProvider = new ApiDataProvider();
		$dataProvider->api = $this->api;
		$dataProvider->url = 'lawsuits';
		$dataProvider->modelClass = LawsuitViewIntegratorDto::class;
		$totalCount = $dataProvider->getTotalCount();
		$this->tester->assertSame(2, $totalCount);
		$this->tester->assertCount(2, $dataProvider->getModels());
		foreach ($dataProvider->getModels() as $model) {
			$this->tester->assertInstanceOf(LawsuitViewIntegratorDto::class, $model);
		}
		$this->tester->assertSame(0, $dataProvider->params[SPIApi::PARAM_PAGE]);
		$this->tester->assertArrayNotHasKey(SPIApi::PARAM_SORT, $dataProvider->getModels());
	}
}

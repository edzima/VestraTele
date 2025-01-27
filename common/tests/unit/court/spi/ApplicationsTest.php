<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\entity\application\ApplicationDTO;
use common\modules\court\modules\spi\entity\application\ApplicationType;
use common\modules\court\modules\spi\entity\application\ApplicationViewDTO;
use common\modules\court\modules\spi\repository\ApplicationsRepository;

/**
 */
class ApplicationsTest extends BaseApiTest {

	protected ApplicationDTO $model;
	private bool $check;
	private bool $create;

	private ApplicationsRepository $repository;

	public function _before(): void {
		parent::_before();
		$this->repository = new ApplicationsRepository($this->api);
	}

	public function testGetApplications(): void {
		$provider = $this->repository->getDataProvider(static::TEST_APPEAL);
		$models = $provider->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertInstanceOf(
				ApplicationViewDTO::class,
				$model
			);
		}
	}

//	public function testCheckEmptyApplication(): void {
//		$this->giveApplication();
//		$this->whenCheck();
//		$this->thenUnsuccessCheck();
//	}

	public function testCheckAndCreateWithValidTestData(): void {
		$this->giveApplication();
		$this->setValidTestData();
		$this->whenCheck();
		$this->thenSuccessCheck();
		$this->whenCreate();
		$this->thenSuccessCreate();
	}

	public function testCheckAndCreateWithoutCourtName(): void {
		$this->giveApplication();
		$this->setValidTestData();
		$this->whenCheck();
		$this->thenSuccessCheck();
		$this->model->courtName = '';
		$this->thenSuccessCreate();
	}

	public function testCheckAndCreateWithoutCourtAndDepartName(): void {
		$this->giveApplication();
		$this->setValidTestData();
		$this->whenCheck();
		$this->model->courtName = '';
		$this->whenCreate();
		$this->thenSuccessCheck();
	}

//	public function testCreateLawsuitApplication(): void {
//		$api = $this->api;
//		$model = new ApplicationDTO();
//		$model->type = ApplicationType::APPLICATION_TYPE_LAWSUIT;
//		$model->courtId = 1;
//		$this->tester->assertTrue($this->api->createApplication($model));
//	}

	protected function giveApplication(array $config = []): void {
		$this->model = $this->repository->createModel($config);
	}

	protected function whenCheck(): void {
		$this->check = $this->repository->checkApplication(static::TEST_APPEAL, $this->model);
	}

	protected function thenSuccessCheck(): void {
		$this->tester->assertTrue($this->check);
	}

	protected function thenUnsuccessCheck(): void {
		$this->tester->assertFalse($this->check);
	}

	private function setValidTestData(string $type = ApplicationType::APPLICATION_TYPE_LAWSUIT): void {
		$this->model->type = $type;
		$this->model->courtId = 1;
		$this->model->repertoryId = 3849;
		$this->model->courtName = 'Sąd Apelacyjny we Wrocławiu';
		$this->model->departmentFullName = 'Wydział I Cywilny';
		$this->model->department = "I";
		$this->model->repertory = 'AGz';
		$this->model->lawsuitNumber = 34;
		$this->model->year = 2012;
		$this->model->roleInLawsuit = 'Biegły';
		$this->model->represented = 'Jan Peszko';
	}

	private function whenCreate(): void {
		$this->create = $this->repository->createApplication(static::TEST_APPEAL, $this->model);
	}

	private function thenSuccessCreate(): void {
		$this->tester->assertTrue($this->create);
	}
}

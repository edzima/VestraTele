<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\entity\lawsuit\NotificationLawsuit;
use common\modules\court\modules\spi\entity\notification\NotificationDTO;
use common\modules\court\modules\spi\entity\notification\NotificationViewDTO;
use common\modules\court\modules\spi\repository\NotificationsRepository;

class NotificationsTest extends BaseApiTest {

	private NotificationsRepository $repository;

	public function _before(): void {
		parent::_before();
		$this->repository = new NotificationsRepository($this->api);
		$this->repository->setAppeal(static::TEST_APPEAL);
	}

	public function testGetDataProvider(): void {
		$models = $this->repository->getDataProvider()->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertInstanceOf(NotificationDTO::class, $model);
		}
	}

	public function testFindModel(): void {
		$models = $this->repository->getDataProvider()->getModels();
		if (!empty($models)) {
			$model = reset($models);
			$this->tester->assertInstanceOf(NotificationDTO::class, $model);
			$model = $this->repository->findModel($model->id);
			$this->tester->assertInstanceOf(NotificationViewDTO::class, $model);
			$this->tester->assertNotEmpty($model->type);
			$lawsuit = $this->tester->assertNotEmpty($model->getLawsuit());
			$this->tester->assertInstanceOf(NotificationLawsuit::class, $lawsuit);
			$this->tester->assertNotEmpty($lawsuit->signature);
		}
	}

	public function testGetUnreadNotificationCount(): void {
		$count = $this->repository
			->getUnread();

		$this->tester->assertIsInt($count);
	}

	public function testReadNotification(): void {
		$model = $this->repository
			->read(102);
	}
}

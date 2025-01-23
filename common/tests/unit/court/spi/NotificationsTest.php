<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\entity\NotificationDTO;
use common\modules\court\modules\spi\entity\NotificationViewDTO;
use common\modules\court\modules\spi\models\lawsuit\NotificationLawsuit;
use common\modules\court\modules\spi\repository\NotificationsRepository;

class NotificationsTest extends BaseApiTest {

	private NotificationsRepository $repository;

	public function _before(): void {
		parent::_before();
		$this->repository = new NotificationsRepository($this->api);
	}

	public function testGetDataProvider(): void {
		$models = $this->repository->getDataProvider(
			static::TEST_APPEAL
		)->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertInstanceOf(NotificationDTO::class, $model);
		}
	}

	public function testFindModel(): void {
		$models = $this->repository->getDataProvider(static::TEST_APPEAL)->getModels();
		if (!empty($models)) {
			$model = reset($models);
			$this->tester->assertInstanceOf(NotificationDTO::class, $model);
			$model = $this->repository->findModel($model->id, static::TEST_APPEAL);
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
			->read(102, static::TEST_APPEAL);
	}
}

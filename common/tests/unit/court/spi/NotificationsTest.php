<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\entity\NotificationDTO;
use common\modules\court\modules\spi\repository\NotificationsRepository;

class NotificationsTest extends BaseApiTest {

	private NotificationsRepository $repository;

	public function _before(): void {
		parent::_before();
		$this->repository = new NotificationsRepository($this->api);
	}

	public function testGetNotifications(): void {
		$notifications = $this->repository->getNotifications()->getModels();
		$this->tester->assertNotEmpty($notifications);
		foreach ($notifications as $notification) {
			$this->tester->assertInstanceOf(NotificationDTO::class, $notification);
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

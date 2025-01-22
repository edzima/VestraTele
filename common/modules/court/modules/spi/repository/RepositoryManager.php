<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\Module;
use Yii;
use yii\base\Component;
use yii\di\Instance;

class RepositoryManager extends Component {

	public $api = 'spiApi';

	/**
	 * @var string|array|callable
	 */
	public $lawsuit = [
		'class' => LawsuitRepository::class,
	];

	/**
	 * @var string|array|callable
	 */
	public $notifications = [
		'class' => NotificationsRepository::class,
	];

	/**
	 * @var string|array|callable
	 */
	public $application = [
		'class' => ApplicationsRepository::class,
	];

	public function init(): void {
		$this->api = Instance::ensure(
			$this->api,
			SPIApi::class,
			Module::getInstance()
		);
		parent::init();
	}

	public function getLawsuits(): LawsuitRepository {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->lawsuit, [$this->api]);
	}

	public function getNotifications(): NotificationsRepository {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->notifications, [$this->api]);
	}

	public function getApplications(): ApplicationsRepository {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->application, [$this->api]);
	}

}

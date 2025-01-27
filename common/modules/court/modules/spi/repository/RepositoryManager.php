<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\Module;
use Yii;
use yii\base\Component;
use yii\di\Instance;

class RepositoryManager extends Component {

	public $api = 'spiApi';
	public $cache = 'cache';
	public string $appeal;

	/**
	 * @var string|array|callable
	 */
	public $document = [
		'class' => DocumentRepository::class,
	];

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
		return Yii::createObject($this->mergeWithCommonConfig($this->lawsuit), [$this->api]);
	}

	public function getNotifications(): NotificationsRepository {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->mergeWithCommonConfig($this->notifications), [$this->api]);
	}

	public function getApplications(): ApplicationsRepository {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->mergeWithCommonConfig($this->application), [$this->api]);
	}

	protected function mergeWithCommonConfig(array $config): array {
		return array_merge($this->commonConfig(), $config);
	}

	protected function commonConfig(): array {
		return [
			'cache' => $this->cache,
			'appeal' => $this->appeal,
		];
	}

	public function getDocuments(): DocumentRepository {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->mergeWithCommonConfig($this->document), [$this->api]);
	}

}

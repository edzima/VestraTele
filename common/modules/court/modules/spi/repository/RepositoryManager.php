<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\components\SPIApi;
use Yii;
use yii\base\Component;

class RepositoryManager extends Component {

	public SPIApi $api;
	public $cache = 'cache';
	public string $appeal;

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

	/**
	 * @var string|array|callable
	 */
	public $courtSessions = [
		'class' => CourtSessionsRepository::class,
	];

	/**
	 * @var string|array|callable
	 */
	public $document = [
		'class' => DocumentRepository::class,
	];

	/**
	 * @var string|array|callable
	 */
	public $parties = [
		'class' => LawsuitPartiesRepository::class,
	];

	/**
	 * @var string|array|callable
	 */
	public $proceedings = [
		'class' => ProceedingsRepository::class,
	];

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

	public function getCourtSessions(): CourtSessionsRepository {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->mergeWithCommonConfig($this->courtSessions), [$this->api]);
	}

	public function getDocuments(): DocumentRepository {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->mergeWithCommonConfig($this->document), [$this->api]);
	}

	public function getParties(): LawsuitPartiesRepository {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->mergeWithCommonConfig($this->parties), [$this->api]);
	}

	public function getProceedings(): ProceedingsRepository {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->mergeWithCommonConfig($this->proceedings), [$this->api]);
	}

}

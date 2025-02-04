<?php

namespace common\modules\court;

use common\modules\court\modules\spi\components\LawsuitSignature;
use common\modules\court\modules\spi\controllers\NotificationController;
use common\modules\court\modules\spi\Module as SpiModule;
use Yii;
use yii\base\ActionEvent;
use yii\base\Module as BaseModule;

/**
 * @property SpiModule|null $spi
 */
class Module extends BaseModule {

	public const SPI_MODULE_NAME = 'spi';
	public const PERMISSION_SPI_LAWSUIT_DETAIL = SpiModule::PERMISSION_SPI_LAWSUIT_DETAIL;

	public $controllerNamespace = 'common\modules\court\controllers';

	public string $signaturePattern = LawsuitSignature::DEFAULT_PATTERN;
	public bool $onlyUserIssues;

	public ?array $spiModuleConfig = [
		'class' => SpiModule::class,
	];

	public function init(): void {
		parent::init();
		//@todo check user has access to spi module
		if (!empty($this->spiModuleConfig)) {
			$config = $this->spiModuleConfig;
			if (!isset($config['class'])) {
				$config['class'] = SpiModule::class;
			}
			$this->setModule(static::SPI_MODULE_NAME, $config);
			Yii::$app->on(
				NotificationController::EVENT_AFTER_ACTION,
				function (ActionEvent $event) {
					$action = $event->action;
					if (
						$action->controller->id === 'notification' &&
						($action->id === 'read' || $action->id === 'view')
					) {
						$route = $action->controller->actionParams;
						array_unshift($route, '/court/lawsuit/read-spi-notification');
						return $action->controller->redirect($route);
					}
				}
			);
		}
	}

	public function getSPI(): ?SpiModule {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->getModule(static::SPI_MODULE_NAME);
	}
}

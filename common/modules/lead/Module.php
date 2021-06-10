<?php

namespace common\modules\lead;

use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\base\Module as BaseModule;
use common\modules\reminder\Module as ReminderModule;

class Module extends BaseModule implements BootstrapInterface {

	protected const REMINDER_MODULE_ID = 'reminder';

	public $controllerNamespace = 'common\modules\lead\controllers';

	public string $userClass;
	/** @var \Closure|array */
	public $userNames;

	public bool $onlyOwner = false;

	public array $reminderModule = [
		'class' => ReminderModule::class,
	];

	public function init(): void {
		parent::init();
		if (!empty($this->reminderModule)) {
			$this->setModule(static::REMINDER_MODULE_ID, $this->reminderModule);
		}
	}

	public function bootstrap($app) {

	}

	public static function userClass(): string {
		return static::instance()->userClass;
	}

	public static function userNames(): array {
		return static::instance()->getUserNames();
	}

	public function getUserNames(): array {
		if (is_array($this->userNames)) {
			return $this->userNames;
		}
		return call_user_func($this->userNames);
	}

	private static function instance(): self {
		$instance = static::getInstance();
		if ($instance === null) {
			throw new InvalidConfigException('Lead module must be configured.');
		}
		return $instance;
	}

}

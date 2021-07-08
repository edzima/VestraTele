<?php

namespace common\modules\lead;

use common\modules\lead\components\LeadManager;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\base\Module as BaseModule;

/**
 * Class Module
 *
 * @property LeadManager $manager
 */
class Module extends BaseModule implements BootstrapInterface {

	protected const REMINDER_MODULE_ID = 'reminder';

	public $controllerNamespace = 'common\modules\lead\controllers';

	public string $userClass;
	/** @var \Closure|array */
	public $userNames;

	public bool $onlyUser = false;
	public bool $allowDelete = true;

	public $components = [
		'manager' => [
			'class' => LeadManager::class,
		],
	];

	public function init(): void {
		parent::init();
		$this->setComponents($this->components);
		if ($this->onlyUser) {
			$this->manager->onlyForUser = true;
		}
	}

	public function bootstrap($app) {

	}

	public static function manager(): LeadManager {
		return static::instance()->manager;
	}

	public static function userClass(): string {
		return static::instance()->userClass;
	}

	public static function userNames(): array {
		return static::instance()->getUserNames();
	}

	public function findUsersNames(array $ids): array {
		if (is_callable($this->findUsersNames)) {
			return call_user_func($this->userNames);
		}
		throw new InvalidConfigException('$userNames must be array or callable.');
	}

	public function getUserNames(): array {
		if (is_array($this->userNames)) {
			return $this->userNames;
		}
		if (is_callable($this->userNames)) {
			return call_user_func($this->userNames);
		}
		throw new InvalidConfigException('$userNames must be array or callable.');
	}

	private static function instance(): self {
		$instance = static::getInstance();
		if ($instance === null) {
			throw new InvalidConfigException('Lead module must be configured.');
		}
		return $instance;
	}

}

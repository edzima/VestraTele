<?php

namespace common\modules\lead;

use Closure;
use common\models\user\User;
use common\modules\lead\components\LeadDialer;
use common\modules\lead\components\LeadManager;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\base\Module as BaseModule;
use yii\db\ActiveRecord;
use yii\di\Instance;

/**
 * Class Module
 *
 * @property LeadManager $manager
 * @property LeadDialer $dialer
 */
class Module extends BaseModule implements BootstrapInterface {

	public $controllerNamespace = 'common\modules\lead\controllers';

	/**
	 * @var string|ActiveRecord
	 */
	public string $userClass = User::class;
	/** @var Closure|array */
	public $userNames;

	public bool $onlyUser = false;
	public bool $allowDelete = true;

	protected $dialer = [
		'class' => LeadDialer::class,
	];

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

	protected function setDialer($dialer): void {
		$this->dialer = $dialer;
	}

	public function getDialer(): ?LeadDialer {
		if ($this->dialer === null) {
			return null;
		}
		if (!is_object($this->dialer)) {
			$this->dialer = Instance::ensure($this->dialer, LeadDialer::class);
		}
		return $this->dialer;
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

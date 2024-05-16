<?php

namespace common\modules\lead;

use Closure;
use common\models\user\User;
use common\modules\lead\components\LeadDialerManager;
use common\modules\lead\components\LeadManager;
use common\modules\lead\components\MarketManager;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\base\Module as BaseModule;
use yii\db\ActiveRecord;
use yii\di\Instance;

/**
 * Class Module
 *
 * @property LeadManager $manager
 * @property LeadDialerManager $dialer
 * @property MarketManager $market
 */
class Module extends BaseModule implements BootstrapInterface {

	public $controllerNamespace = 'common\modules\lead\controllers';

	/**
	 * @var string|ActiveRecord
	 */
	public string $userClass = User::class;
	/** @var Closure|array */
	public $userNames = [];

	public bool $onlyUser = false;
	public bool $allowDelete = true;

	protected $dialer = [
		'class' => LeadDialerManager::class,
	];

	protected $market = [
		'class' => MarketManager::class,
	];

	protected $manager = [
		'class' => LeadManager::class,
	];

	public function init(): void {
		parent::init();
		if ($this->onlyUser) {
			$this->getManager()->onlyForUser = true;
		}
	}

	protected function setDialer($dialer): void {
		$this->dialer = $dialer;
	}

	public function getDialer(): ?LeadDialerManager {
		if ($this->dialer === null) {
			return null;
		}
		if (!is_object($this->dialer)) {
			$this->dialer = Instance::ensure($this->dialer, LeadDialerManager::class);
		}
		return $this->dialer;
	}

	protected function setMarket($market): void {
		$this->market = $market;
	}

	public function getMarket(): ?MarketManager {
		if ($this->market === null) {
			return null;
		}
		if (!is_object($this->market)) {
			$this->market = Instance::ensure($this->market, MarketManager::class);
		}
		return $this->market;
	}

	protected function setManager($manager): void {
		$this->$manager = $manager;
	}

	public function getManager(): LeadManager {
		if (!is_object($this->manager)) {
			$this->manager = Instance::ensure($this->manager, LeadManager::class);
		}
		return $this->manager;
	}

	public function bootstrap($app) {

	}

	public static function manager(): LeadManager {
		return static::instance()->getManager();
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

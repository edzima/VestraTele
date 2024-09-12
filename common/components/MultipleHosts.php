<?php

namespace common\components;

use common\helpers\ArrayHelper;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;

class MultipleHosts extends Component {

	/**
	 * @var array|Host[]
	 */
	public array $hosts = [];

	public function init() {
		parent::init();
		if (empty($this->hosts)) {
			throw new InvalidConfigException('$hosts must be set.');
		}
		$this->initHosts();
	}

	protected function initHosts(): void {
		$hosts = [];
		foreach ($this->hosts as $key => $host) {
			/**
			 * @var Host $host
			 */
			$host = Instance::ensure($host, Host::class);
			if (empty($host->name)) {
				if (!is_string($key)) {
					throw new InvalidConfigException('Host $name must be set.');
				}
				$host->name = $key;
			}
			$hosts[] = $host;
		}
		$this->hosts = $hosts;
	}

	public function getNames(): array {
		return ArrayHelper::getColumn($this->hosts, 'name');
	}

	public function getHost(string $name): ?Host {
		foreach ($this->hosts as $host) {
			if ($host->name === $name) {
				return $host;
			}
		}
		return null;
	}

}

<?php

namespace backend\modules\issue\models;

use common\components\Host;
use Yii;
use yii\base\BaseObject;

class HostIssueStats extends BaseObject {

	public array $statConfig = [];
	protected const MULTIPLE_HOST_COMPONENT_ID = 'multipleHosts';

	private Host $host;
	private ?IssueStats $stats = null;

	public function __construct(Host $host, $config = []) {
		$this->host = $host;
		parent::__construct($config);
	}

	public function getHostName(): string {
		return $this->host->name;
	}

	public function getStats(): IssueStats {
		if ($this->stats === null) {
			$this->stats = new IssueStats($this->statConfig);
			$this->stats->db = $this->host->getDb();
		}
		return $this->stats;
	}

	/**
	 * @param string[] $hostNames
	 * @param array $config
	 * @return static[]
	 */
	public static function createMultiple(array $hostNames = [], array $config = []): array {
		$models = [];
		$hosts = static::hosts();
		if (!empty($hostNames)) {
			$hosts = array_filter($hosts, function (Host $host) use ($hostNames): bool {
				return in_array($host->name, $hostNames);
			});
		}
		foreach ($hosts as $host) {
			$models[] = new static($host, $config);
		}
		return $models;
	}

	/**
	 * @return Host[]
	 */
	public static function hosts(): array {
		$multiple = Yii::$app->get(static::MULTIPLE_HOST_COMPONENT_ID, false);
		if ($multiple) {
			return $multiple->hosts;
		}
		return [];
	}

	public static function hasHosts(): bool {
		return !empty(static::hosts());
	}
}

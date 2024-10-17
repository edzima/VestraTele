<?php

namespace common\components\rbac;

use yii\base\BaseObject;

class AccessPermission extends BaseObject {

	public string $separator = ':';
	public string $prefix;
	public string $app;
	public string $action;
	public string $modelName;
	public ?string $modelId = null;
	public string $name;

	public function generate(bool $withId = true): void {
		$parts = [
			$this->prefix,
			$this->app,
			$this->modelName,
			$this->action,
		];
		if ($withId && $this->modelId !== null) {
			$parts[] = $this->modelId;
		}
		array_walk($parts, function (&$part) {
			$part = $this->encode($part);
		});
		$this->name = implode($this->separator, $parts);
	}

	public function explode(): bool {
		$parts = explode($this->separator, $this->name);
		if (!$parts || count($parts) < 4) {
			return false;
		}
		array_walk($parts, function (&$part) {
			$part = $this->decode($part);
		});
		$this->prefix = $parts[0];
		$this->app = $parts[1];
		$this->modelName = $parts[2];
		$this->action = $parts[3];
		$this->modelId = $parts[4] ?? null;
		return true;
	}

	protected function encode(string $name): string {
		return str_replace($this->separator, ';', $name);
	}

	protected function decode(string $name): string {
		return str_replace(';', $this->separator, $name);
	}

	public function getName(): string {
		return $this->name;
	}

	public const COMPARE_ALL = 'all';
	public const COMPARE_WITHOUT_ID = 'without_id';
	public const COMPARE_WITHOUT_APP_AND_ACTION = 'without_app_and_action';
	public const COMPARE_PREFIX_WITH_MODEL_NAME = 'model_name_and_prefix';
	public const COMPARE_PREFIX = 'prefix';

	public static function compare(self $a, self $b, string $type = self::COMPARE_ALL): bool {
		switch ($type) {
			case self::COMPARE_ALL:
				return
					$a->prefix === $b->prefix
					&& $a->app === $b->app
					&& $a->modelName === $b->modelName
					&& $a->action === $b->action
					&& $a->modelId === $b->modelId;

			case self::COMPARE_WITHOUT_ID:
				return
					$a->prefix === $b->prefix
					&& $a->app === $b->app
					&& $a->modelName === $b->modelName
					&& $a->action === $b->action;

			case self::COMPARE_WITHOUT_APP_AND_ACTION:
				return
					$a->prefix === $b->prefix
					&& $a->modelName === $b->modelName
					&& $a->modelId === $b->modelId;
			case self::COMPARE_PREFIX_WITH_MODEL_NAME:
				return
					$a->prefix === $b->prefix
					&& $a->modelName === $b->modelName;
			case self::COMPARE_PREFIX:
				return
					$a->prefix === $b->prefix;
		}
		return false;
	}
}

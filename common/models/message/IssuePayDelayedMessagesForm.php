<?php

namespace common\models\message;

class IssuePayDelayedMessagesForm extends IssuePayMessagesForm {

	public const KEY_DEMAND_WHICH = 'demandWhich';
	public string $whichDemand;

	protected static function mainKeys(): array {
		return [
			'issue',
			'settlement',
			'pay',
			'delayed',
		];
	}

	public function keysParts(): array {
		$parts = parent::keysParts();
		array_unshift($parts, [static::KEY_DEMAND_WHICH => $this->whichDemand]);
		return $parts;
	}

}

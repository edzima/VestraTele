<?php

namespace common\models\message;

use common\components\message\MessageTemplate;

class IssuePayDelayedMessagesForm extends IssuePayMessagesForm {

	public const KEY_DEMAND_WHICH = 'demandWhich';
	public string $whichDemand;

	public string $dateFormat = 'Y-m-d';

	public bool $withSettlementTypeInKey = false;
	public bool $bindIssueType = true;

	protected static function mainKeys(): array {
		return [
			'issue',
			'settlement',
			'pay',
			'delayed',
		];
	}

	protected function parsePay(MessageTemplate $template) {
		parent::parsePay($template);
		$template->parseBody(['deadlineAt' => $this->pay->getDeadlineAt()->format($this->dateFormat)]);
	}

	public function keysParts(string $type): array {
		$parts = parent::keysParts($type);
		if ($type === static::KEY_CUSTOMER) {
			array_unshift($parts, [static::KEY_DEMAND_WHICH => $this->whichDemand]);
		}
		return $parts;
	}

}

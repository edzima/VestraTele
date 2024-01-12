<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\models\issue\IssueCost;
use Yii;

class IssueCostMessagesForm extends IssueMessagesForm {

	protected IssueCost $cost;

	protected static function mainKeys(): array {
		return [
			'issue',
			'settlement',
			'cost',
		];
	}

	public function setCost(IssueCost $cost): void {
		$this->cost = $cost;
	}

	protected function parseTemplate(MessageTemplate $template): void {
		parent::parseTemplate($template);
		$this->parseCost($template);
	}

	protected function parseCost(MessageTemplate $template) {
		$type = $this->cost->getTypeName();
		$value = Yii::$app->formatter->asCurrency($this->cost->getValue());
		$template->parseSubject([
			'costType' => $type,
			'costValue' => $value,
		]);
		$template->parseBody([
			'costType' => $type,
			'costValue' => $value,
		]);
	}

}

<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\models\issue\IssuePayInterface;
use Yii;

class IssuePayPayedMessagesForm extends IssueSettlementMessagesForm {

	private IssuePayInterface $pay;

	public bool $withSettlementTypeInKey = false;

	protected static function mainKeys(): array {
		return [
			'issue',
			'settlement',
			'pay',
			'payed',
		];
	}

	public function setPay(IssuePayInterface $pay): void {
		$this->pay = $pay;
		if ($this->settlement === null || $this->settlement->getId() !== $pay->calculation->getId()) {
			$this->setSettlement($pay->calculation);
		}
	}

	protected function parseTemplate(MessageTemplate $template): void {
		parent::parseTemplate($template);
		$this->parsePay($template);
	}

	protected function parsePay(MessageTemplate $template) {
		$value = Yii::$app->formatter->asCurrency($this->pay->getValue());
		$template->parseSubject(['payValue' => $value]);
		$template->parseBody(['payValue' => $value]);
	}
}

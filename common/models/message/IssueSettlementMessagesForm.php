<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\components\message\MessageTemplateKeyHelper;
use common\models\issue\IssueSettlement;
use Yii;

class IssueSettlementMessagesForm extends IssueMessagesForm {

	protected static function mainKeys(): array {
		return [
			'issue',
			'settlement',
		];
	}

	public bool $withSettlementTypeInKey = true;

	protected ?IssueSettlement $settlement = null;

	public function setSettlement(IssueSettlement $settlement) {
		$this->settlement = $settlement;
		if ($this->issue === null
			|| $this->issue->getIssueId() !== $settlement->getIssueId()
		) {
			$this->setIssue($this->settlement);
		}
	}

	protected function parseTemplate(MessageTemplate $template): void {
		parent::parseTemplate($template);
		$this->parseSettlement($template);
	}

	protected function parseSettlement(MessageTemplate $template) {
		$template->parseBody(['settlementLink' => $this->settlement->getFrontendUrl()]);
		$template->parseBody(['settlementValue' => Yii::$app->formatter->asCurrency($this->settlement->getValue())]);
	}

	public function keysParts(): array {
		$parts = parent::keysParts();
		if ($this->withSettlementTypeInKey) {
			$parts[] = static::settlementTypeKeyPart($this->settlement->getType());
		}
		return $parts;
	}

	public static function generateKey(string $type, string $key, ?array $issueTypesIds = null, ?int $settlementType = null): string {
		$parts = array_merge((array) $type, static::mainKeys(), (array) $key);
		if ($settlementType !== null) {
			$parts[] = static::settlementTypeKeyPart($settlementType);
		}
		if (!empty($issueTypesIds)) {
			$parts[] = MessageTemplateKeyHelper::issueTypesKeyPart($issueTypesIds);
		}
		return MessageTemplateKeyHelper::generateKey($parts);
	}

	public static function settlementTypeKeyPart(int $type): string {
		return "type:$type";
	}

}

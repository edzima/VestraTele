<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\components\message\MessageTemplateKeyHelper;
use common\helpers\Html;
use common\models\issue\IssueSettlement;
use Yii;

class IssueSettlementMessagesForm extends IssueMessagesForm {

	public const KEY_SETTLEMENT_TYPE = 'settlementType';

	protected static function mainKeys(): array {
		return [
			'issue',
			'settlement',
		];
	}

	public bool $withSettlementTypeInKey = true;

	protected ?IssueSettlement $settlement = null;

	public function init(): void {
		parent::init();
		if ($this->fromNameTemplate === '{appName}') {
			$this->fromNameTemplate = '{appName} - ' . Yii::t('settlement', 'Settlements');
		}
	}

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
		$template->parseBody(['settlementLink' => $this->getSettlementLink()]);
		$template->parseBody(['settlementValue' => Yii::$app->formatter->asCurrency($this->settlement->getValue())]);
	}

	protected function parsePrimaryButton(MessageTemplate $template): void {
		$template->primaryButtonText = $this->settlement->getIssueName() . ' - ' . $this->settlement->getTypeName();
		$template->primaryButtonHref = $this->settlement->getFrontendUrl();
	}

	protected function getSettlementLink(): string {
		return Html::a(
			$this->settlement->getIssueName() . ' - ' . $this->settlement->getTypeName(),
			$this->settlement->getFrontendUrl()
		);
	}

	public function keysParts(string $type): array {
		$parts = parent::keysParts($type);
		if ($this->withSettlementTypeInKey) {
			$parts[static::KEY_SETTLEMENT_TYPE] = $this->settlement->getTypeId();
		}
		return $parts;
	}

	public static function generateKey(string $type, string $key, ?array $issueTypesIds = null, ?int $settlementType = null): string {
		$parts = array_merge((array) $type, static::mainKeys(), (array) $key);
		if ($settlementType !== null) {
			$parts[static::KEY_SETTLEMENT_TYPE] = $settlementType;
		}
		if (!empty($issueTypesIds)) {
			$parts[] = static::issueTypesKeyPart($issueTypesIds);
		}
		return MessageTemplateKeyHelper::generateKey($parts);
	}

}

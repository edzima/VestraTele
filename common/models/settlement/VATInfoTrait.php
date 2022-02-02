<?php

namespace common\models\settlement;

use Decimal\Decimal;
use Yii;

trait VATInfoTrait {

	public function vatAttributeLabels(): array {
		return [
			'valueWithVAT' => Yii::t('settlement', 'Value with VAT'),
			'valueWithoutVAT' => Yii::t('settlement', 'Value without VAT'),
		];
	}

	public function getValueWithoutVAT(): Decimal {
		$vat = $this->getVAT();
		if ($vat !== null) {
			return Yii::$app->tax->netto($this->getValueWithVAT(), $this->getVAT());
		}
		return $this->getValueWithVAT();
	}

	public function getValueWithVAT(): Decimal {
		return new Decimal($this->{$this->valueWithVATAttribute()});
	}

	public function getVAT(): ?Decimal {
		if ($this->hasVAT()) {
			return new Decimal($this->{$this->vatAttribute()});
		}
		return null;
	}

	public function getValueVAT(): Decimal {
		return $this->getValueWithVAT()->sub($this->getValueWithoutVAT());
	}

	public function getVATPercent(): ?string {
		if ($this->hasVAT()) {
			return Yii::$app->formatter->asPercent($this->{$this->vatAttribute()} / 100);
		}
		return null;
	}

	public function hasVAT(): bool {
		return !empty($this->{$this->vatAttribute()}) && $this->{$this->vatAttribute()} > 0;
	}

	protected function vatAttribute(): string {
		return 'vat';
	}

	protected function valueWithVATAttribute(): string {
		return 'value';
	}

}

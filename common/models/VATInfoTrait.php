<?php

namespace common\models;

use Decimal\Decimal;
use Yii;

trait VATInfoTrait {

	public function getValueWithoutVAT(): Decimal {
		return Yii::$app->tax->netto($this->getValueWithVAT(), $this->getVAT());
	}

	public function getValueWithVAT(): Decimal {
		return new Decimal($this->{$this->valueWithVATAttribute()});
	}

	public function getVAT(): Decimal {
		return new Decimal($this->{$this->vatAttribute()});
	}

	public function getVATPercent(): string {
		return Yii::$app->formatter->asPercent($this->{$this->vatAttribute()} / 100);
	}

	protected function vatAttribute(): string {
		return 'vat';
	}

	protected function valueWithVATAttribute():string{
		return 'value';
	}

}

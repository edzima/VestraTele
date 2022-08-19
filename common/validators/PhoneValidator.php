<?php

namespace common\validators;

use yii\validators\StringValidator;

class PhoneValidator extends StringValidator {

	public $min = 3;

	public function validateAttribute($model, $attribute) {
		$model->{$attribute} = $this->filterValue($model->{$attribute});
		parent::validateAttribute($model, $attribute);
	}

	protected function validateValue($value) {
		return parent::validateValue($this->filterValue($value));
	}

	protected function filterValue($value): string {
		$value = ltrim($value, '0');
		return preg_replace('/[^0-9.]+/', '', $value);
	}
}

<?php

namespace common\widgets;

use borales\extensions\phoneInput\PhoneInput as BasePhoneInput;
use Yii;

class PhoneInput extends BasePhoneInput {

	public ?array $preferredCountries;

	public function init(): void {
		if (!isset($this->preferredCountries)) {
			$this->preferredCountries = Yii::$app->params['phoneInput.preferredCountries'];
		}

		if ($this->preferredCountries !== null) {
			$this->jsOptions['preferredCountries'] = $this->preferredCountries;
		}

		parent::init();
	}

}

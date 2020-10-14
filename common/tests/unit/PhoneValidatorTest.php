<?php

namespace common\tests\unit;

use common\models\user\UserProfile;
use udokmeci\yii2PhoneValidator\PhoneValidator;

class PhoneValidatorTest extends Unit {

	public function testHomePhone(): void {
		$userProfile = new UserProfile();
		$userProfile->phone = '737456766';
		static::assertTrue($this->createValidator()->validateAttribute($userProfile, 'phone'));
	}

	private function createValidator(): PhoneValidator {
		$validator = new PhoneValidator();
		$validator->country = 'PL';
		return $validator;
	}

}

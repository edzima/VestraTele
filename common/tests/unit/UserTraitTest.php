<?php

namespace common\tests\unit;

use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\User;
use common\models\user\UserTraitAssign;
use yii\db\IntegrityException;

class UserTraitTest extends Unit {

	public function _fixtures(): array {
		return [
			'user' => UserFixtureHelper::customer(),
			'customer-assign' => UserFixtureHelper::customerTraits(),
			'trait' => UserFixtureHelper::traits(),
		];
	}

	private function getUserWithoutTraits(): User {
		return User::findOne(UserFixtureHelper::CUSTOMER_TOMMY_JOHNS);
	}

	private function getUserWithTrait(): User {
		// TRAIT_BAILIFF
		return User::findOne(UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID);
	}

	private function getUserWithTraits(): User {
		// TRAIT_BAILIFF, TRAIT_DISABILITY_RESULT_OF_CASE
		return User::findOne(UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID);
	}

	public function testAssignUserSingleTrait(): void {
		$user = $this->getUserWithoutTraits();
		$traitsToAssign = [UserFixtureHelper::TRAIT_COMMISSION_REFUND];
		UserTraitAssign::assignUser($user->id, $traitsToAssign);
		$this->tester->seeRecord(UserTraitAssign::class, ['trait_id' => UserFixtureHelper::TRAIT_COMMISSION_REFUND, 'user_id' => $user->id]);
		$this->tester->dontSeeRecord(UserTraitAssign::class, ['trait_id' => UserFixtureHelper::TRAIT_DISABILITY_RESULT_OF_CASE, 'user_id' => $user->id]);
	}

	public function testAssignUserMultipleTraits(): void {
		$user = $this->getUserWithoutTraits();
		$traitsToAssign = [UserFixtureHelper::TRAIT_COMMISSION_REFUND, UserFixtureHelper::TRAIT_BAILIFF];
		UserTraitAssign::assignUser($user->id, $traitsToAssign);
		$this->tester->seeRecord(UserTraitAssign::class, ['trait_id' => UserFixtureHelper::TRAIT_BAILIFF, 'user_id' => $user->id]);
		$this->tester->seeRecord(UserTraitAssign::class, ['trait_id' => UserFixtureHelper::TRAIT_COMMISSION_REFUND, 'user_id' => $user->id]);
		$this->tester->dontSeeRecord(UserTraitAssign::class, ['trait_id' => UserFixtureHelper::TRAIT_DISABILITY_RESULT_OF_CASE, 'user_id' => $user->id]);
	}

	public function testAssignUserTraitSameTrait(): void {
		$user = $this->getUserWithTrait();
		$this->tester->expectThrowable(IntegrityException::class, function () use ($user) {
			UserTraitAssign::assignUser($user->id, [UserFixtureHelper::TRAIT_BAILIFF]);
		});
		$this->tester->dontSeeRecord(UserTraitAssign::class, ['trait_id' => UserFixtureHelper::TRAIT_COMMISSION_REFUND, 'user_id' => $user->id]);
	}

	public function testAssignUserNoTraits(): void {
		$user = $this->getUserWithTraits();
		UserTraitAssign::assignUser($user->id, []);
		$this->tester->dontSeeRecord(UserTraitAssign::class, ['trait_id' => UserFixtureHelper::TRAIT_COMMISSION_REFUND, 'user_id' => $user->id]);
		$this->tester->dontSeeRecord(UserTraitAssign::class, ['trait_id' => UserFixtureHelper::TRAIT_BAILIFF, 'user_id' => $user->id]);
		$this->tester->dontSeeRecord(UserTraitAssign::class, ['trait_id' => UserFixtureHelper::TRAIT_DISABILITY_RESULT_OF_CASE, 'user_id' => $user->id]);
		$this->tester->seeRecord(UserTraitAssign::class, ['trait_id' => UserFixtureHelper::TRAIT_BAILIFF, 'user_id' => $this->getUserWithTrait()->id]);
	}

}

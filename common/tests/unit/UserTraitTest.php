<?php

namespace common\tests\unit;

use common\fixtures\UserFixture;
use common\fixtures\UserTraitFixture;
use common\models\user\User;
use common\models\user\UserTrait;
use yii\db\IntegrityException;

class UserTraitTest extends Unit {

	public function _before() {
		parent::_before();
		$this->tester->haveFixtures([
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . '/user/user.php',
			],
			'user-trait' => [
				'class' => UserTraitFixture::class,
				'dataFile' => codecept_data_dir() . 'user/user_trait.php',
			],
		]);
	}

	private function getUserWithoutTraits(): User {
		return $this->tester->grabFixture('user', 0);
	}

	private function getUserWithTrait(): User {
		// TRAIT_BAILIFF
		return $this->tester->grabFixture('user', 1);
	}

	private function getUserWithTraits(): User {
		// TRAIT_BAILIFF, TRAIT_DISABILITY_RESULT_OF_CASE
		return $this->tester->grabFixture('user', 2);
	}

	public function testAssignUserSingleTrait(): void {
		$user = $this->tester->grabFixture('user', 0);
		$traitsToAssign = [UserTrait::TRAIT_LIABILITIES];
		UserTrait::assignUser($user->id, $traitsToAssign);
		$this->tester->seeRecord(UserTrait::class, ['trait_id' => UserTrait::TRAIT_LIABILITIES, 'user_id' => $user->id]);
	}

	public function testAssignUserMultipleTraits(): void {
		$user = $this->tester->grabFixture('user', 0);
		$traitsToAssign = [UserTrait::TRAIT_LIABILITIES, UserTrait::TRAIT_BAILIFF];
		UserTrait::assignUser($user->id, $traitsToAssign);

		$this->tester->seeRecord(UserTrait::class, ['trait_id' => UserTrait::TRAIT_BAILIFF, 'user_id' => $user->id]);
		$this->tester->seeRecord(UserTrait::class, ['trait_id' => UserTrait::TRAIT_LIABILITIES, 'user_id' => $user->id]);
	}

	public function testAssignUserTraitSameTrait(): void {
		$user = $this->getUserWithTrait();
		$this->tester->expectThrowable(IntegrityException::class, function () use ($user) {
			UserTrait::assignUser($user->id, [UserTrait::TRAIT_BAILIFF]);
		});
		$this->tester->dontSeeRecord(UserTrait::class, ['trait_id' => UserTrait::TRAIT_LIABILITIES, 'user_id' => $user->id]);
	}

	public function testAssignUserNoTraits(): void {
		$user = $this->getUserWithTraits();
		UserTrait::assignUser($user->id, []);
		$this->tester->dontSeeRecord(UserTrait::class, ['trait_id' => UserTrait::TRAIT_LIABILITIES, 'user_id' => $user->id]);
		$this->tester->dontSeeRecord(UserTrait::class, ['trait_id' => UserTrait::TRAIT_BAILIFF, 'user_id' => $user->id]);
		$this->tester->dontSeeRecord(UserTrait::class, ['trait_id' => UserTrait::TRAIT_DISABILITY_RESULT_OF_CASE, 'user_id' => $user->id]);
		$this->tester->seeRecord(UserTrait::class, ['trait_id' => UserTrait::TRAIT_BAILIFF, 'user_id' => $this->getUserWithTrait()->id]);
	}

}

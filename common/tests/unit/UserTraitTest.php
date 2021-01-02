<?php

namespace common\tests\unit;

use common\fixtures\UserFixture;
use common\fixtures\UserTraitFixture;
use common\models\user\UserTrait;
use common\tests\UnitTester;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use Yii;

class UserTraitTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	public function _before() {
		parent::_before();
		$this->tester->haveFixtures([
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php',
			],
			'user-trait' => [
				'class' => UserTraitFixture::class,
				'dataFile' => codecept_data_dir() . 'user/user_trait.php',
			],

		]);
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
	public function testAssignUserTraitWithoutDelete(): void {
		$user = $this->tester->grabFixture('user', 1);
		$traitsToAssign = [UserTrait::TRAIT_LIABILITIES];
		UserTrait::assignUser($user->id, $traitsToAssign, false);
		$this->tester->seeRecord(UserTrait::class, ['trait_id' => UserTrait::TRAIT_BAILIFF, 'user_id' => $user->id]);
		$this->tester->seeRecord(UserTrait::class, ['trait_id' => UserTrait::TRAIT_LIABILITIES, 'user_id' => $user->id]);
	}
	public function testAssignUserNoTraits(): void {
		$user = $this->tester->grabFixture('user', 0);
		$traitsToAssign = [];
		UserTrait::assignUser($user->id, $traitsToAssign);
		$this->tester->dontSeeRecord(UserTrait::class, ['user_id' => $user->id]);
	}
	public function testAssignUserNoTraitsWithoutDelete(): void {
		$user = $this->tester->grabFixture('user', 1);
		$traitsToAssign = [];
		UserTrait::assignUser($user->id, $traitsToAssign, false);
		$this->tester->seeRecord(UserTrait::class, ['trait_id' => UserTrait::TRAIT_BAILIFF, 'user_id' => $user->id]);
	}


}

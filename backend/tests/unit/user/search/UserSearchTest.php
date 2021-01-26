<?php

namespace backend\tests\unit\user\search;

use backend\modules\user\models\search\UserSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\User;
use common\tests\_support\UnitSearchModelTrait;
use Yii;

class UserSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before(): void {
		parent::_before();
		$this->tester->haveFixtures(array_merge(
				UserFixtureHelper::workers(),
				['user-profile' => UserFixtureHelper::profile('user')],
			)
		);

		$this->model = $this->createModel();
	}

	public function testEmpty(): void {
		$this->assertTotalCount(13);
	}

	public function testRole(): void {
		$this->assertTotalCount(5, ['role' => [User::ROLE_AGENT]]);
		$this->assertTotalCount(4, ['role' => [User::ROLE_LAWYER]]);
		$this->assertTotalCount(4, ['role' => [User::ROLE_TELEMARKETER]]);

		$this->assertTotalCount(0, ['role' => [User::ROLE_MANAGER]]);
		$lawyer = $this->tester->grabFixture(UserFixtureHelper::WORKER_LAWYER, '0');
		Yii::$app->authManager->assign(Yii::$app->authManager->getRole(User::ROLE_MANAGER), $lawyer->id);
		$this->assertTotalCount(1, ['role' => [User::ROLE_MANAGER]]);
		$this->assertTotalCount(1, ['role' => [User::ROLE_MANAGER, User::ROLE_LAWYER]]);

		$this->assertTotalCount(0, ['permission' => [User::PERMISSION_NOTE]]);
		Yii::$app->authManager->assign(Yii::$app->authManager->getPermission(User::PERMISSION_NOTE), $lawyer->id);
		$this->assertTotalCount(1, ['permission' => [User::PERMISSION_NOTE]]);
		$this->assertTotalCount(1, ['role' => [User::ROLE_LAWYER], 'permission' => [User::PERMISSION_NOTE]]);
	}

	public function testPhone(): void {
		$this->assertTotalCount(1, ['phone' => '+48 673 222 110']);
		$this->assertTotalCount(1, ['phone' => '+48-673-222-110']);
		$this->assertTotalCount(1, ['phone' => '     +48 673 222 110    ']);
		$this->assertTotalCount(1, ['phone' => '+48 - - -673 ----222-110']);
		$this->assertTotalCount(0, ['phone' => '+48+673+222-110']);
	}
	public function testPhone2(): void {
		$this->assertTotalCount(1, ['phone' => '541-211-980']);
		$this->assertTotalCount(1, ['phone' => '541 - 211 - 980']);
		$this->assertTotalCount(1, ['phone' => '541211980']);
	}

	public function testStatus(): void {
		$this->assertTotalCount(3, ['status' => User::STATUS_INACTIVE]);
		$this->assertTotalCount(7, ['status' => User::STATUS_ACTIVE]);
	}

	protected function createModel(): UserSearch {
		return new UserSearch();
	}
}

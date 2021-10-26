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
		$this->model = $this->createModel();
	}

	public function _fixtures(): array {
		return array_merge(
			UserFixtureHelper::workers(),
			UserFixtureHelper::profile('user'),
		);
	}

	public function testRole(): void {
		$keys = $this->search(['role' => [User::ROLE_AGENT]])->getKeys();
		$this->tester->assertNotEmpty($keys);
		foreach ($keys as $key) {
			$this->tester->assertTrue(Yii::$app->authManager->checkAccess($key, User::ROLE_AGENT));
		}
		$keys = $this->search(['role' => [User::ROLE_MANAGER]])->getKeys();
		$this->tester->assertEmpty($keys);
	}

	public function testPermissions(): void {
		$keys = $this->search(['permission' => [User::PERMISSION_NOTE]])->getKeys();
		$this->tester->assertEmpty($keys);
		Yii::$app->authManager->assign(Yii::$app->authManager->getPermission(User::PERMISSION_NOTE), User::find()->one()->id);
		$keys = $this->search(['permission' => [User::PERMISSION_NOTE]])->getKeys();
		$this->tester->assertNotEmpty($keys);
		foreach ($keys as $key) {
			$this->tester->assertTrue(Yii::$app->authManager->checkAccess($key, User::PERMISSION_NOTE));
		}
	}

	public function testPhoneNumber(): void {
		$models = $this->search(['phone' => '+48 673 223 110'])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var User $model */
			$this->tester->assertSame('+48 673 223 110', $model->profile->phone);
		}

		$models = $this->search(['phone' => '+48 673-223-110'])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var User $model */
			$this->tester->assertSame('+48 673 223 110', $model->profile->phone);
		}
		$models = $this->search(['phone' => '+48 673223110'])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var User $model */
			$this->tester->assertSame('+48 673 223 110', $model->profile->phone);
		}
	}

	public function testPhone2(): void {
		$models = $this->search(['phone' => '541-211-980'])->getModels();
		$this->tester->assertNotEmpty($models);

		$models = $this->search(['phone' => '999'])->getModels();
		$this->tester->assertEmpty($models);
	}

	public function testPhoneWithLastname(): void {
		$models = $this->search([
			'phone' => '673223110',
			'lastname' => 'Wayne',
		])->getModels();

		$this->assertNotEmpty($models);
		/** @var User $user */
		$user = reset($models);
		$this->tester->assertSame('Wayne', $user->profile->lastname);
		$this->tester->assertSame('+48 673 223 110', $user->profile->phone);

		$models = $this->search([
			'phone' => '999',
			'lastname' => 'Wayne',
		])->getModels();
		$this->tester->assertEmpty($models);
	}

	public function testStatus(): void {
		$models = $this->search(['status' => User::STATUS_ACTIVE])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var User $model */
			$this->tester->assertSame(User::STATUS_ACTIVE, $model->status);
		}
		$models = $this->search(['status' => User::STATUS_INACTIVE])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var User $model */
			$this->tester->assertSame(User::STATUS_INACTIVE, $model->status);
		}
	}

	protected function createModel(): UserSearch {
		return new UserSearch();
	}
}

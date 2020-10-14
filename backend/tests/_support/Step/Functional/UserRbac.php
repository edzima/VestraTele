<?php

namespace backend\tests\Step\Functional;

use backend\tests\FunctionalTester;
use Codeception\Scenario;
use common\models\user\User;
use Yii;

abstract class UserRbac extends FunctionalTester {

	private User $user;

	protected const USERNAME = 'user_rbac';
	protected const PASSWORD = 'user_rbac_password';

	public function __construct(Scenario $scenario) {
		$this->user = $this->createUser();
		$this->assignRoles();
		$this->assignPermissions();
		parent::__construct($scenario);
	}



	public function _after(FunctionalTester $I): void {
		Yii::$app->authManager->revokeAll($this->user->id);
	}

	public function getUser(): User {
		return $this->user;
	}

	protected function getRoles(): array {
		return [];
	}

	protected function getPermissions(): array {
		return [];
	}

	final public function amLoggedIn(): void {
		$I = $this;
		$I->amOnPage('/site/login');
		$I->fillField('Username', static::USERNAME);
		$I->fillField('Password', static::PASSWORD);
		$I->click('#login-form button[type=submit]');
		$I->see(static::USERNAME);
	}

	private function assignRoles(): void {
		$auth = Yii::$app->authManager;
		foreach ($this->getRoles() as $roleName) {
			try {
				$auth->assign($auth->getRole($roleName), $this->user->id);
			} catch (\Exception $exception) {
			}
		}
	}

	private function assignPermissions(): void {
		$auth = Yii::$app->authManager;
		foreach ($this->getPermissions() as $permission) {
			try {
				$auth->assign($auth->getPermission($permission), $this->user->id);
			} catch (\Exception $exception) {
			}
		}
	}

	private function createUser(): User {
		$user = new User();
		$user->password = static::PASSWORD;
		$user->username = static::USERNAME;
		$user->status = User::STATUS_ACTIVE;
		$user->setPassword(static::PASSWORD);
		$user->generateAuthKey();
		$user->save();
		return $user;
	}

}

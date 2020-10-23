<?php

namespace common\tests\_support;

use Codeception\Scenario;
use common\models\user\User;
use Yii;

trait UserRbacActor {

	private User $user;

	protected function getUsername(): string {
		return 'user_rbac';
	}

	protected function getPassword(): string {
		return 'user_rbac_password';
	}

	public function __construct(Scenario $scenario) {
		$this->user = $this->createUser();
		$this->assignRoles();
		$this->assignPermissions();
		parent::__construct($scenario);
	}

	public function _after(): void {
		codecept_debug('revoke all');
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
		$I->fillField('Username', $this->getUsername());
		$I->fillField('Password', $this->getPassword());
		$I->click('#login-form button[type=submit]');
		$this->checkIsLogged();
	}

	protected function checkIsLogged(): void {
		$this->see($this->getUsername());
	}

	private function assignRoles(): void {
		$auth = Yii::$app->authManager;
		foreach ($this->getRoles() as $roleName) {
			try {
				$auth->assign($auth->getRole($roleName), $this->user->id);
			} catch (\Exception $exception) {
				codecept_debug($exception->getMessage());
			}
		}
	}

	private function assignPermissions(): void {
		$auth = Yii::$app->authManager;
		foreach ($this->getPermissions() as $permission) {
			try {
				$auth->assign($auth->getPermission($permission), $this->user->id);
			} catch (\Exception $exception) {
				codecept_debug($exception->getMessage());
			}
		}
	}

	private function createUser(): User {
		$user = new User();
		$user->username = $this->getUsername();
		$user->setPassword($this->getPassword());
		$user->status = User::STATUS_ACTIVE;
		$user->generateAuthKey();
		$user->save();
		return $user;
	}
}

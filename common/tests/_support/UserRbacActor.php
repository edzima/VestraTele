<?php

namespace common\tests\_support;

use common\models\user\User;
use Yii;
use yii\rbac\ManagerInterface;

trait UserRbacActor {

	private ?User $user = null;

	protected function getUsername(): string {
		return 'user_rbac';
	}

	protected function getPassword(): string {
		return 'user_rbac_password';
	}

	public function getUser(): User {
		if ($this->user === null) {
			$user = Yii::$app->user;
			if ($user && $user->getIdentity()) {
				$this->user = Yii::$app->user->getIdentity();
				codecept_debug('Load user from identity: ' . $this->user->username);
			} else {
				$this->user = $this->createUser();
				codecept_debug('Create user: ' . $this->user->username);
			}
			$this->revokeAll();
			$this->assignRoles();
			$this->assignPermissions();
		}
		return $this->user;
	}

	private function revokeAll(): void {
		$this->getAuth()->revokeAll($this->getUser()->id);
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
		$I->fillField('Username', $this->getUser()->username);
		$I->fillField('Password', $this->getPassword());
		$I->click('#login-form button[type=submit]');
		$this->checkIsLogged();
	}

	protected function checkIsLogged(): void {
		$this->see($this->getUsername());
	}

	private function assignRoles(): void {
		foreach ($this->getRoles() as $role) {
			$this->assignRole($role);
		}
	}

	private function assignPermissions(): void {
		foreach ($this->getPermissions() as $permission) {
			$this->assignPermission($permission);
		}
	}

	protected function assignRole(string $name): void {
		$auth = $this->getAuth();
		try {
			$auth->assign($auth->getRole($name), $this->getUser()->id);
			codecept_debug('Assign role: ' . $name);
		} catch (\Exception $exception) {
			codecept_debug($exception->getMessage());
		}
	}

	public function assignPermission(string $name): void {
		$auth = $this->getAuth();
		try {
			$auth->assign($auth->getPermission($name), $this->getUser()->id);
			codecept_debug('Assign permission: ' . $name);
		} catch (\Exception $exception) {
			codecept_debug($exception->getMessage());
		}
	}

	private function createUser(): User {
		User::deleteAll(['username' => $this->getUsername()]);
		$user = new User();
		$user->username = $this->getUsername();
		$user->setPassword($this->getPassword());
		$user->status = User::STATUS_ACTIVE;
		$user->generateAuthKey();
		$user->save();
		return $user;
	}

	protected function getAuth(): ManagerInterface {
		return Yii::$app->authManager;
	}
}

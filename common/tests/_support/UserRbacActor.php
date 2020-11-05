<?php

namespace common\tests\_support;

use common\models\user\User;
use Yii;
use yii\helpers\ArrayHelper;

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
			$this->user = $this->createUser();
			codecept_debug('Create user: ' . $this->user->username);
			$this->revokeAll();
			$this->assignRoles();
			$this->assignPermissions();
		}
		return $this->user;
	}

	private function revokeAll(): void {
		Yii::$app->authManager->revokeAll($this->getUser()->id);
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
		$auth = Yii::$app->authManager;
		foreach ($this->getRoles() as $roleName) {
			try {
				$auth->assign($auth->getRole($roleName), $this->getUser()->id);
			} catch (\Exception $exception) {
				codecept_debug($exception->getMessage());
			}
		}
		codecept_debug('Assigned roles:');
		codecept_debug(ArrayHelper::getColumn($auth->getRolesByUser($this->getUser()->id), 'name'));
	}

	private function assignPermissions(): void {
		$auth = Yii::$app->authManager;
		foreach ($this->getPermissions() as $permission) {
			try {
				$auth->assign($auth->getPermission($permission), $this->getUser()->id);
			} catch (\Exception $exception) {
				codecept_debug($exception->getMessage());
			}
		}
		codecept_debug('Assigned permissions:');
		codecept_debug(ArrayHelper::getColumn($auth->getPermissionsByUser($this->getUser()->id), 'name'));
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

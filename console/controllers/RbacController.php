<?php

namespace console\controllers;

use common\models\user\User;
use common\rbac\OwnModelRule;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class RbacController extends Controller {

	public array $roles = [
		User::ROLE_ARCHIVE,
		User::ROLE_ISSUE,
		User::ROLE_LOGS,
		User::ROLE_MEET,
		User::ROLE_NEWS,
		User::ROLE_NOTE,
		User::ROLE_CUSTOMER_SERVICE,

		User::ROLE_AGENT,
		User::ROLE_BOOKKEEPER,
		User::ROLE_LAWYER,
		User::ROLE_TELEMARKETER,

		User::ROLE_CLIENT,
		User::ROLE_VICTIM,

	];

	public function actionInit() {
		$auth = Yii::$app->authManager;
		$auth->removeAll();

		$user = $auth->createRole(User::ROLE_USER);
		$auth->add($user);

		// own model rule
		$ownModelRule = new OwnModelRule();
		$auth->add($ownModelRule);

		$manager = $auth->createRole(User::ROLE_MANAGER);
		$auth->add($manager);
		$auth->addChild($manager, $user);

		$loginToBackend = $auth->createPermission('loginToBackend');
		$auth->add($loginToBackend);
		$auth->addChild($manager, $loginToBackend);

		$admin = $auth->createRole(User::ROLE_ADMINISTRATOR);
		$auth->add($admin);
		$auth->addChild($admin, $manager);

		foreach ($this->roles as $roleName) {
			$role = $auth->createRole($roleName);
			$auth->add($role);
			$auth->addChild($admin, $role);
		}

		$auth->assign($admin, 1);

		$this->addDelayedPays();

		Console::output('Success! RBAC roles has been added.');
	}

	private function addDelayedPays(): void {
		$auth = Yii::$app->authManager;
		$role = $auth->createRole(User::ROLE_BOOKKEEPER_DELAYED);
		$auth->add($role);
		$bookKeeper = $auth->getRole(User::ROLE_BOOKKEEPER);
		$auth->addChild($bookKeeper, $role);
	}
	
}

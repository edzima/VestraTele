<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\User;
use common\rbac\OwnModelRule;

class RbacController extends Controller {

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

		$auth->assign($admin, 1);

		Console::output('Success! RBAC roles has been added.');
	}

	public function actionAddLayer() {
		$auth = Yii::$app->authManager;
		$manageCause = $auth->createPermission('manageCause');
		$auth->add($manageCause);

		$manager = $auth->getRole('manager');

		$layer = $auth->createRole(User::ROLE_LAYER);
		$auth->add($layer);
		$auth->addChild($layer, $manageCause);
		$auth->addChild($manager, $layer);

		Console::output('Success! RBAC roles layer has been added.');
	}

	public function actionAddAgentAndTele() {

		$auth = Yii::$app->authManager;
		$manager = $auth->getRole('manager');

		//add agent role
		$agent = $auth->createRole(User::ROLE_AGENT);
		$auth->add($agent);
		$auth->addChild($manager, $agent);

		//add telemarketer role
		$tele = $auth->createRole(User::ROLE_TELEMARKETER);
		$auth->add($tele);
		$auth->addChild($manager, $tele);

		Console::output('Success! RBAC roles (tele, agent)  has been added.');
	}

	public function actionChangeTypWorkWithRole() {
		$users = User::find()->all();

		$auth = Yii::$app->authManager;

		$tele = $auth->getRole(User::ROLE_TELEMARKETER);
		$agent = $auth->getRole(User::ROLE_AGENT);

		foreach ($users as $user) {
			$auth->revokeAll($user->id);
			// Console::output($user->typ_work.$user->username);
			switch ($user->typ_work) {
				case "T":
					Console::output("User is T with id: " . $user->id);
					$auth->assign($tele, $user->id);
					break;
				case "P":
					Console::output("User is P with id: " . $user->id);
					$auth->assign($agent, $user->id);
					break;
				case "A":
					Console::output("Admin");
					$auth->assign($auth->getRole(User::ROLE_MANAGER), $user->id);
					break;
			}
		}
		$auth->assign($auth->getRole(User::ROLE_ADMINISTRATOR), 1);
	}

	public function actionAddBookKeeper() {
		$auth = Yii::$app->authManager;
		$auth->add($auth->createRole(User::ROLE_BOOKKEEPER));
		$auth->addChild($auth->getRole(User::ROLE_ADMINISTRATOR), 	$auth->getRole(User::ROLE_BOOKKEEPER));
	}

	public function actionAddMeet() {
		$auth = Yii::$app->authManager;
		$auth->add($auth->createRole(User::ROLE_MEET));
		$auth->addChild($auth->getRole(User::ROLE_ADMINISTRATOR), 	$auth->getRole(User::ROLE_MEET));
	}

}

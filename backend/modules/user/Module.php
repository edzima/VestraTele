<?php

namespace backend\modules\user;

use common\models\user\User;
use yii\base\Module as BaseModule;
use yii\filters\AccessControl;

class Module extends BaseModule {

	public $controllerNamespace = 'backend\modules\user\controllers';

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'controllers' => ['user/user', 'user/customer', 'user/worker'],
						'actions' => ['view'],
						'allow' => true,
						'roles' => ['manager'],
					],
					[
						'controllers' => ['user/customer'],
						'actions' => ['index', 'create', 'update'],
						'allow' => true,
						'roles' => ['manager'],
					],
					[
						'controllers' => ['user/worker'],
						'actions' => ['index'],
						'allow' => true,
						'roles' => ['manager'],
					],
					[
						'controllers' => ['user/worker'],
						'actions' => ['index', 'create', 'update', 'create-from-json', 'request-password-reset'],
						'allow' => true,
						'permissions' => [User::PERMISSION_WORKERS],
					],
					[
						'controllers' => ['user/worker'],
						'actions' => ['hierarchy'],
						'allow' => true,
						'permissions' => [User::PERMISSION_WORKERS_HIERARCHY],
					],
					[
						'controllers' => ['user/trait'],
						'allow' => true,
						'permissions' => [User::PERMISSION_USER_TRAITS],
					],
					[
						'allow' => true,
						'roles' => ['administrator'],
					],
				],
			],

		];
	}

}

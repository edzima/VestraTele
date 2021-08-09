<?php

namespace backend\modules\user;

use common\models\user\Worker;
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
						'actions' => ['index', 'create', 'update'],
						'allow' => true,
						'permissions' => [Worker::PERMISSION_WORKERS],
					],
					[
						'controllers' => ['user/relation'],
						'allow' => true,
						'permissions' => [Worker::PERMISSION_USER_RELATION],
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

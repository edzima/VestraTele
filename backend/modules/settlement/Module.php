<?php

namespace backend\modules\settlement;

use common\models\user\Worker;
use yii\base\Module as BaseModule;
use yii\filters\AccessControl;

class Module extends BaseModule {

	public $controllerNamespace = 'backend\modules\settlement\controllers';

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'controllers' => ['settlement/cost'],
						'permissions' => [Worker::PERMISSION_COST],
					],
					[
						'allow' => true,
						'actions' => ['to-create','create', 'view', 'update'],
						'controllers' => ['settlement/calculation'],
						'permissions' => [Worker::PERMISSION_CALCULATION_TO_CREATE],
					],
					[
						'allow' => true,
						'controllers' => ['settlement/calculation-problem'],
						'permissions' => [Worker::PERMISSION_CALCULATION_PROBLEMS],
					],
					[
						'allow' => true,
						'controllers' => ['settlement/pay'],
						'permissions' => [Worker::PERMISSION_PAY],
					],
					[
						'allow' => true,
						'roles' => [Worker::ROLE_BOOKKEEPER],
					],
				],
			],

		];
	}
}

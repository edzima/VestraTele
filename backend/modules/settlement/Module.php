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
						'controllers' => ['settlement/calculation-min-count'],
						'permissions' => [Worker::PERMISSION_CALCULATION_TO_CREATE],
					],
					[
						'allow' => true,
						'actions' => ['to-create', 'create', 'create-administrative', 'view', 'update', 'pays', 'delete', 'owner', 'index'],
						'controllers' => ['settlement/calculation'],
						'permissions' => [Worker::PERMISSION_CALCULATION_TO_CREATE],
					],
					[
						'allow' => true,
						'actions' => ['without-provisions'],
						'controllers' => ['settlement/calculation'],
						'permissions' => [Worker::PERMISSION_PROVISION],
					],
					[
						'allow' => true,
						'controllers' => ['settlement/calculation-problem'],
						'permissions' => [Worker::PERMISSION_CALCULATION_PROBLEMS],
					],
					[
						'allow' => true,
						'actions' => ['pay-provisions'],
						'controllers' => ['settlement/pay'],
						'permissions' => [Worker::PERMISSION_ISSUE],
					],
					[
						'allow' => true,
						'actions' => ['delayed'],
						'controllers' => ['settlement/pay'],
						'permissions' => [Worker::PERMISSION_PAYS_DELAYED],
					],
					[
						'allow' => true,
						'controllers' => ['settlement/pay'],
						'permissions' => [Worker::PERMISSION_PAY],
					],
					[
						'allow' => true,
						'controllers' => ['settlement/pay-received'],
						'permissions' => [Worker::PERMISSION_PAY_RECEIVED],
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

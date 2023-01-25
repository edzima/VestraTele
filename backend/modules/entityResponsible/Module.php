<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-01
 * Time: 22:39
 */

namespace backend\modules\entityResponsible;

use common\models\user\Worker;
use yii\base\Module as BaseModule;
use yii\filters\AccessControl;

class Module extends BaseModule {

	public $controllerNamespace = 'backend\modules\entityResponsible\controllers';

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::ROLE_ADMINISTRATOR],
						'permissions' => [Worker::PERMISSION_ENTITY_RESPONSIBLE_MANAGER],
					],
				],
			],
		];
	}

}

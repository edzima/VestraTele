<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-01
 * Time: 22:39
 */

namespace backend\modules\provision;

use common\models\user\Worker;
use yii\base\Module as BaseModule;
use yii\filters\AccessControl;

class Module extends BaseModule {

	public $controllerNamespace = 'backend\modules\provision\controllers';

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::ROLE_BOOKKEEPER],
					],
				],
			],
		];
	}

}

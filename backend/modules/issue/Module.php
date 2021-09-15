<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-01
 * Time: 22:39
 */

namespace backend\modules\issue;

use common\models\user\Worker;
use yii\base\Module as BaseModule;
use yii\filters\AccessControl;

class Module extends BaseModule {

	public $controllerNamespace = 'backend\modules\issue\controllers';

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'controllers' => ['issue/meet'],
						'permissions' => [Worker::PERMISSION_MEET],
					],
					[
						'allow' => true,
						'controllers' => ['issue/note'],
						'permissions' => [Worker::PERMISSION_NOTE],
					],
					[
						'allow' => true,
						'controllers' => ['issue/summon'],
						'permissions' => [Worker::PERMISSION_SUMMON],
					],
					[
						'allow' => true,
						'controllers' => ['issue/issue'],
						'actions' => ['delete'],
						'permissions' => [Worker::PERMISSION_ISSUE_DELETE],
					],
					[
						'allow' => false,
						'controllers' => ['issue/issue'],
						'actions' => ['delete'],
					],
					[
						'allow' => true,
						'controllers' => ['issue/issue', 'issue/type', 'issue/stage', 'issue/user'],
						'permissions' => [Worker::PERMISSION_ISSUE],
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

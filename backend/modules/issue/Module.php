<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-01
 * Time: 22:39
 */

namespace backend\modules\issue;

use common\models\issue\event\IssueUserEvent;
use common\models\user\Worker;
use Yii;
use yii\base\Module as BaseModule;
use yii\filters\AccessControl;

class Module extends BaseModule {

	public $controllerNamespace = 'backend\modules\issue\controllers';

	public function init() {
		parent::init();
		$this->on(IssueUserEvent::WILDCARD_EVENT, function (IssueUserEvent $event): void {
			Yii::$app->provisions->onIssueUserEvent($event);
		});
	}

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'controllers' => ['issue/meet'],
						'roles' => [Worker::ROLE_ADMINISTRATOR],
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
						'controllers' => ['issue/summon-type', 'issue/summon-doc'],
						'permissions' => [Worker::PERMISSION_SUMMON_MANAGER],
					],
					[
						'allow' => true,
						'controllers' => ['issue/issue'],
						'actions' => ['delete'],
						'permissions' => [Worker::PERMISSION_ISSUE_DELETE],
					],
					[
						'allow' => true,
						'controllers' => ['issue/relation'],
						'permissions' => [Worker::PERMISSION_ISSUE_CREATE],
					],
					[
						'allow' => true,
						'controllers' => ['issue/claim'],
						'permissions' => [Worker::PERMISSION_ISSUE_CLAIM],
					],
					[
						'allow' => false,
						'controllers' => ['issue/issue'],
						'actions' => ['delete'],
					],
					[
						'allow' => true,
						'controllers' => ['issue/issue', 'issue/type', 'issue/stage', 'issue/user', 'issue/tag'],
						'permissions' => [Worker::PERMISSION_ISSUE],
					],
					[
						'allow' => true,
						'controllers' => ['issue/sms'],
						'permissions' => [Worker::PERMISSION_SMS],
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

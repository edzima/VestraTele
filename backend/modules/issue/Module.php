<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-01
 * Time: 22:39
 */

namespace backend\modules\issue;

use backend\modules\issue\models\IssueUserChangeHandler;
use common\models\issue\event\IssueUserEvent;
use common\models\issue\Issue;
use common\models\user\Worker;
use Yii;
use yii\base\Module as BaseModule;
use yii\filters\AccessControl;

class Module extends BaseModule {

	public $controllerNamespace = 'backend\modules\issue\controllers';

	public const PERMISSION_ISSUE_CHART = 'issue.chart';

	public function init() {
		parent::init();
		IssueUserEvent::on(Issue::class, IssueUserEvent::WILDCARD_EVENT, static function (IssueUserEvent $event): void {
			$model = new IssueUserChangeHandler($event);
			$model->user_id = Yii::$app->user->getId();
			$model->parse();
		});
	}

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [

					[
						'allow' => true,
						'controllers' => ['issue/issue', 'issue/user'],
						'permissions' => [Worker::PERMISSION_ISSUE],
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
						'controllers' => ['issue/summon-doc', 'issue/summon-doc-link'],
						'permissions' => [Worker::PERMISSION_SUMMON_DOC_MANAGER],
					],
					[
						'allow' => true,
						'controllers' => ['issue/summon-type', 'issue/summon-doc', 'issue/summon-doc-link'],
						'permissions' => [Worker::PERMISSION_SUMMON_MANAGER],
					],
					[
						'allow' => true,
						'controllers' => ['issue/issue', 'issue/user', 'issue/tag', 'issue/tag-type'],
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
						'allow' => true,
						'controllers' => ['issue/issue', 'issue/archive'],
						'permissions' => [Worker::PERMISSION_ARCHIVE],
					],
					[
						'allow' => true,
						'controllers' => ['issue/stage', 'issue/stage-type'],
						'permissions' => [Worker::PERMISSION_ISSUE_STAGE_MANAGER],
					],
					[
						'allow' => true,
						'controllers' => ['issue/tag'],
						'actions' => ['issue'],
						'permissions' => [Worker::PERMISSION_ISSUE_CREATE],
					],
					[
						'allow' => true,
						'controllers' => ['issue/type', 'issue/stage-type'],
						'permissions' => [Worker::PERMISSION_ISSUE_TYPE_MANAGER],
					],
					[
						'allow' => true,
						'controllers' => ['issue/type'],
						'actions' => ['stages-list'],
						'permissions' => [Worker::PERMISSION_ISSUE_CREATE],
					],
					[
						'allow' => true,
						'controllers' => ['issue/tag', 'issue/tag-type'],
						'permissions' => [Worker::PERMISSION_ISSUE_TAG_MANAGER],
					],
					[
						'allow' => true,
						'controllers' => ['issue/stat'],
						'permissions' => [Worker::PERMISSION_ISSUE_STAT],
					],
					[
						'allow' => true,
						'controllers' => ['issue/shipment-poczta-polska'],
						'permissions' => [Worker::PERMISSION_ISSUE_SHIPMENT],
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
					[
						'allow' => false,
						'controllers' => ['issue/issue', 'issue/user', 'issue/tag', 'issue/tag-type'],
						'actions' => ['delete'],
						'permissions' => [Worker::PERMISSION_ISSUE],
					],
				],
			],

		];
	}
}

<?php

namespace console\controllers;

use common\models\issue\IssueUser;
use common\models\user\User;
use common\models\user\Worker;
use yii\console\Controller;
use yii\helpers\Console;

class UserController extends Controller {

	public function actionClearNotIssue(): void {
		$workersIDs = Worker::find()
			->select('id')
			->column();
		$customerIDS = IssueUser::find()->withTypes(IssueUser::TYPES_CUSTOMERS)
			->select('user_id')
			->column();
		$ids = array_merge($workersIDs, $customerIDS);
		$ids[] = 1;

		$count = User::deleteAll(['NOT IN', 'id', $ids]);

		Console::output('Delete User: ' . $count);
	}

}

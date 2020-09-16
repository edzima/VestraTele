<?php

namespace backend\modules\user\controllers;

use backend\modules\user\models\search\WorkerUserSearch;
use backend\modules\user\models\WorkerUserForm;
use common\models\user\Worker;

class WorkerController extends UserController {

	public $searchModel = WorkerUserSearch::class;
	public $formModel = WorkerUserForm::class;
	public $model = Worker::class;
}

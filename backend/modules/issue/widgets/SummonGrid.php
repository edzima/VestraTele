<?php

namespace backend\modules\issue\widgets;

use backend\modules\issue\controllers\SummonController;
use backend\widgets\IssueColumn;
use common\models\issue\Summon;
use common\models\user\Worker;
use common\widgets\grid\SummonGrid as BaseSummonGrid;
use Yii;

class SummonGrid extends BaseSummonGrid {

	public string $issueColumn = IssueColumn::class;

	public function init(): void {
		/** @see SummonController */
		$this->actionColumn['controller'] = '/issue/summon';
		$this->actionColumn['visibleButtons'] = [
			'update' => function (Summon $model): bool {
				return $model->isForUser(Yii::$app->user->getId()) || Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER);
			},
			'delete' => function (Summon $model): bool {
				return $model->isOwner(Yii::$app->user->getId()) || Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER);
			},
		];
		parent::init();
	}
}

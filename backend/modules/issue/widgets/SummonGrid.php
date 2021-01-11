<?php

namespace backend\modules\issue\widgets;

use backend\widgets\IssueColumn;
use common\models\issue\Summon;
use common\widgets\grid\SummonGrid as BaseSummonGrid;
use Yii;

class SummonGrid extends BaseSummonGrid {

	public string $issueColumn = IssueColumn::class;

	public function init(): void {
		$this->actionColumn['controller'] = '/issue/summon';
		$this->actionColumn['visibleButtons'] = [
			'update' => function (Summon $model): bool {
				return $model->isOwner(Yii::$app->user->getId());
			},
			'delete' => function (Summon $model): bool {
				return $model->isOwner(Yii::$app->user->getId());
			},
		];
		parent::init();
	}
}

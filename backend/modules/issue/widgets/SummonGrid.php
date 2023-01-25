<?php

namespace backend\modules\issue\widgets;

use backend\modules\issue\controllers\SummonController;
use backend\widgets\IssueColumn;
use common\models\issue\Summon;
use common\widgets\grid\SummonGrid as BaseSummonGrid;

class SummonGrid extends BaseSummonGrid {

	public string $issueColumn = IssueColumn::class;

	public function init(): void {
		/** @see SummonController */
		$this->actionColumn['controller'] = '/issue/summon';
		$this->actionColumn['visibleButtons'] = [
			'update' => function (Summon $model): bool {
				return SummonController::canUpdate($model);
			},
			'delete' => function (Summon $model): bool {
				return SummonController::canDelete($model);
			},
		];
		parent::init();
	}
}

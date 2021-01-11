<?php

namespace frontend\widgets;

use common\models\issue\Summon;
use common\widgets\grid\SummonGrid as BaseSummonGrid;
use Yii;

class SummonGrid extends BaseSummonGrid {

	public string $issueColumn = IssueColumn::class;

	public bool $withContractor = false;

	public function init(): void {
		$this->actionColumn['controller'] = '/summon';
		$this->actionColumn['template'] = '{view} {update}';
		$this->actionColumn['visibleButtons']['update'] = static function (Summon $model): bool {
			return $model->isForUser(Yii::$app->user->getId());
		};
		parent::init();
	}

}

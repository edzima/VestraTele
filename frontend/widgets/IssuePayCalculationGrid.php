<?php

namespace frontend\widgets;

use common\widgets\grid\ActionColumn;
use common\widgets\grid\IssuePayCalculationGrid as BaseIssuePayCalculationGrid;
use common\widgets\grid\IssueTypeColumn;

class IssuePayCalculationGrid extends BaseIssuePayCalculationGrid {

	public string $issueColumn = IssueColumn::class;
	public bool $withOwner = false;
	public bool $withStageOnCreate = false;
	public string $valueTypeIssueType = IssueTypeColumn::VALUE_NAME_WITH_SHORT;

	protected function actionColumn(): array {
		return [
			'class' => ActionColumn::class,
			'template' => '{view}',
			'controller' => 'settlement',
		];
	}
}

<?php

namespace backend\modules\issue\widgets;

use backend\modules\settlement\widgets\IssuePayCalculationGrid;
use common\widgets\grid\SettlementsGrids;

class IssueSettlementsGrid extends SettlementsGrids {

	protected const DEFAULT_GRID_CLASS = IssuePayCalculationGrid::class;

	public array $gridOptions = [
		'withIssue' => false,
		'summary' => '',
		'showOnEmpty' => false,
		'withAgent' => false,
		'withIssueType' => false,
		'withCustomer' => false,
		'withDates' => false,
		'withDetails' => true,
		'emptyText' => false,
		'withCaption' => true,
		'withProblems' => false,
	];
}

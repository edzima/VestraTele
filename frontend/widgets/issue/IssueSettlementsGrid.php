<?php

namespace frontend\widgets\issue;

use common\widgets\grid\SettlementsGrids;
use frontend\widgets\IssuePayCalculationGrid;
use Yii;

class IssueSettlementsGrid extends SettlementsGrids {

	protected const DEFAULT_GRID_CLASS = IssuePayCalculationGrid::class;

	public array $gridOptions = [
		'withIssue' => false,
		'withAgent' => false,
		'withCaption' => true,
		'withIssueType' => false,
		'withCustomer' => false,
		'withDates' => false,
		'showOnEmpty' => false,
		'emptyText' => false,
		'summary' => '',
		'withIsPercentage' => false,
	];

	public function init(): void {
		parent::init();
		if (!isset($this->gridOptions['userProvisionsId'])) {
			$this->gridOptions['userProvisionsId'] = Yii::$app->user->getId();
		}
	}
}

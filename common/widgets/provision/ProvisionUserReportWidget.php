<?php

namespace common\widgets\provision;

use common\models\provision\ProvisionReportSummary;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\IssueColumn;
use yii\base\Widget;

class ProvisionUserReportWidget extends Widget {

	public ProvisionReportSummary $model;

	public array $actionColumn = [
		'class' => ActionColumn::class,
		'template' => '{delete}',
	];

	public array $issueColumn = [
		'class' => IssueColumn::class,
	];

	public function init() {
		parent::init();
		$this->issueColumn = array_merge($this->issueColumn, $this->defaultIssueColumn());
	}

	public function run(): string {
		return $this->render('user-report', [
			'model' => $this->model,
			'issueColumn' => $this->issueColumn,
			'actionColumn' => $this->actionColumn,
		]);
	}

	public function defaultIssueColumn(): array {
		return [
			'withTags' => false,
		];
	}

}

<?php

namespace common\widgets\provision;

use common\helpers\Html;
use common\models\issue\IssueCost;
use common\models\issue\IssueInterface;
use common\models\provision\ProvisionReportSummary;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\DataColumn;
use Yii;
use yii\base\Widget;

class ProvisionUserReportWidget extends Widget {

	public ProvisionReportSummary $model;

	public array $actionColumn = [
		'class' => ActionColumn::class,
		'template' => '{delete}',
	];

	public array $issueColumn = [];

	public ?string $issueRoute = '/issue/view';

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

	protected function defaultIssueColumn(): array {
		return [
			'class' => DataColumn::class,
			'attribute' => 'issue_id',
			'format' => $this->issueRoute ? 'html' : 'text',
			'label' => Yii::t('issue', 'Issue'),
			'value' => function ($model): ?string {
				$issue = null;
				if ($model instanceof IssueInterface) {
					$issue = $model->getIssueModel();
				}
				if ($model instanceof IssueCost) {
					$issue = $model->issue;
				}
				if ($issue === null) {
					return null;
				}
				if ($this->issueRoute) {
					return Html::a($issue->getIssueName(), [$this->issueRoute, 'id' => $issue->getIssueId()]);
				}
				return $issue->getIssueName();
			},
		];
	}

}

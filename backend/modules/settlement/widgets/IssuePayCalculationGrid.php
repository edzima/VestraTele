<?php

namespace backend\modules\settlement\widgets;

use backend\helpers\Html;
use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use backend\widgets\IssueTypeColumn;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerDataColumn;
use Yii;
use yii\base\InvalidConfigException;

class IssuePayCalculationGrid extends GridView {

	public $id = 'calculation-grid';

	public bool $withIssue = true;
	public bool $withCustomer = true;

	/**
	 * @var IssuePayCalculationSearch
	 */
	public $filterModel;

	public function init(): void {
		if ($this->filterModel !== null && !$this->filterModel instanceof IssuePayCalculationSearch) {
			throw new InvalidConfigException('$filter model must be instance of: ' . IssuePayCalculationSearch::class . '.');
		}
		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		parent::init();
	}

	public function defaultColumns(): array {
		return [
			[
				'class' => ActionColumn::class,
				'template' => '{problem-status} {provision} {view} {update} {delete}',
				'buttons' => [
					'problem-status' => static function (string $url, IssuePayCalculation $model): string {
						if ($model->isPayed()) {
							return '';
						}
						return Html::a(Html::icon('warning'), $url);
					},
					'provision' => static function (string $url, IssuePayCalculation $model) {
						return Yii::$app->user->can(User::PERMISSION_PROVISION)
							? Html::a('<span class="glyphicon glyphicon-usd"></span>',
								['/provision/settlement/set', 'id' => $model->id],
								[
									'title' => 'Prowizje',
									'aria-label' => 'Prowizje',
									'data-pjax' => '0',
								])
							: '';
					},
				],
			],
			[
				'class' => IssueColumn::class,
				'visible' => $this->withIssue,
			],
			[
				'class' => IssueTypeColumn::class,
				'attribute' => 'issue_type_id',
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => $this->filterModel::getTypesNames(),
			],
			[
				'attribute' => 'problem_status',
				'value' => 'problemStatusName',
				'filter' => $this->filterModel::getProblemStatusesNames(),
				'visible' => $this->filterModel->problem_status !== null,
			],
			[
				'class' => CustomerDataColumn::class,
				'visible' => $this->withCustomer,
			],
			[
				'attribute' => 'providerName',
				'filter' => $this->filterModel::getProvidersTypesNames(),
			],
			[
				'attribute' => 'value',
				'format' => 'currency',
			],
			[
				'attribute' => 'valueToPay',
				'format' => 'currency',
				'visible' => function (IssuePayCalculation $model): bool {
					return $model->isPayed();
				},
			],
			[
				'attribute' => 'created_at',
				'format' => 'date',
			],
			[
				'attribute' => 'updated_at',
				'format' => 'date',
			],
			[
				'attribute' => 'payment_at',
				'format' => 'date',
			],
		];
	}

}

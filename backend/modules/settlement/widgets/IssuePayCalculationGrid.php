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
use Decimal\Decimal;
use Yii;
use yii\base\InvalidConfigException;

class IssuePayCalculationGrid extends GridView {

	public $id = 'calculation-grid';

	public $showPageSummary = true;

	public bool $withIssue = true;
	public bool $withCustomer = true;
	public bool $withIssueType = true;
	public bool $withDates = true;

	public ?int $userProvisionsId = null;

	/**
	 * @var IssuePayCalculationSearch
	 */
	public $filterModel;

	public function init(): void {
		if (!empty($this->id) && !isset($this->options['id'])) {
			$this->options['id'] = $this->id;
		}
		if ($this->filterModel !== null && !$this->filterModel instanceof IssuePayCalculationSearch) {
			throw new InvalidConfigException('$filter model must be instance of: ' . IssuePayCalculationSearch::class . '.');
		}
		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		if (empty($this->rowOptions)) {
			$this->rowOptions = function (IssuePayCalculation $model): array {
				return $this->defaultRowOptions($model);
			};
		}
		parent::init();
	}

	protected function defaultRowOptions(IssuePayCalculation $model): array {
		return [];
		$options = [];
		if ($model->isPayed()) {
			Html::addCssClass($options, 'payed-row');
		} else {
			if ($model->isDelayed()) {
				Html::addCssClass($options, 'delayed-row');
			}
			if ($model->hasProblemStatus()) {
				Html::addCssClass($options, 'problem-row');
			}
		}
		return $options;
	}

	public function defaultColumns(): array {
		return [
			[
				'class' => ActionColumn::class,
				'template' => '{provision} {problem-status} {view} {update} {delete}',
				'controller' => '/settlement/calculation',
				'buttons' => [
					'problem-status' => static function (string $url, IssuePayCalculation $model): string {
						if ($model->isPayed()) {
							return '';
						}
						return Html::a(Html::icon('warning-sign'),
							['/settlement/calculation-problem/set', 'id' => $model->id],
							[
								'title' => Yii::t('backend', 'Set problem status'),
								'aria-label' => Yii::t('backend', 'Set problem status'),
							]);
					},
					'provision' => static function (string $url, IssuePayCalculation $model) {
						return Yii::$app->user->can(User::PERMISSION_PROVISION)
							? Html::a(Html::icon('usd'),
								['/provision/settlement/set', 'id' => $model->id],
								[
									'title' => Yii::t('backend', 'Set provisions'),
									'aria-label' => Yii::t('backend', 'Set provisions'),
								])
							: '';
					},
				],

				'contentOptions' => [
					'class' => 'd-inline-flex width-100 justify-center',
				],
			],
			[
				'class' => IssueColumn::class,
				'visible' => $this->withIssue,
			],
			[
				'class' => CustomerDataColumn::class,
				'visible' => $this->withCustomer,
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => IssuePayCalculation::getTypesNames(),
			],

			[
				'attribute' => 'problem_status',
				'value' => 'problemStatusName',
				'filter' => IssuePayCalculation::getProblemStatusesNames(),
				'visible' => $this->filterModel && ($this->filterModel->problem_status !== null || $this->filterModel->onlyWithProblems),
			],
			[
				'class' => IssueTypeColumn::class,
				'label' => Yii::t('backend', 'Issue type'),
				'attribute' => 'issue_type_id',
				'visible' => $this->withIssueType,

			],
			[
				'attribute' => 'stage_id',
				'label' => Yii::t('backend', 'Issue stage on create'),
				'value' => 'stageName',
				'filter' => IssuePayCalculation::getStagesNames(),
			],
			[
				'attribute' => 'value',
				'format' => 'currency',
				'pageSummary' => true,
			],
			[
				'attribute' => 'valueToPay',
				'format' => 'currency',
				'pageSummary' => true,
				'pageSummaryFunc' => function (array $decimals): Decimal {
					$sum = new Decimal(0);
					foreach ($decimals as $decimal) {
						$sum = $sum->add($decimal);
					}
					return $sum;
				},
			],
			[
				'attribute' => 'providerName',
				'filter' => IssuePayCalculation::getProvidersTypesNames(),
				'value' => function (IssuePayCalculation $model): string {
					if ($model->provider_type === IssuePayCalculation::PROVIDER_RESPONSIBLE_ENTITY) {
						return $model->getProviderName();
					}
					return IssuePayCalculation::getProvidersTypesNames()[IssuePayCalculation::PROVIDER_CLIENT];
				},
			],
			[
				'attribute' => 'userProvisionsSum',
				'format' => 'currency',
				'visible' => $this->userProvisionsId !== null,
				'pageSummary' => true,
				'value' => function (IssuePayCalculation $model): Decimal {
					if ($this->userProvisionsId === null) {
						return new Decimal(0);
					}
					return $model->getUserProvisionsSum($this->userProvisionsId);
				},
				'pageSummaryFunc' => function ($decimals): Decimal {
					$sum = new Decimal(0);
					foreach ($decimals as $decimal) {
						$sum = $sum->add($decimal);
					}
					return $sum;
				},
			],
			[
				'attribute' => 'userProvisionsSumNotPay',
				'format' => 'currency',
				'visible' => $this->userProvisionsId !== null,
				'pageSummary' => true,
				'value' => function (IssuePayCalculation $model): Decimal {
					if ($this->userProvisionsId === null) {
						return new Decimal(0);
					}
					return $model->getUserProvisionsSumNotPay($this->userProvisionsId);
				},
				'pageSummaryFunc' => function ($decimals): Decimal {
					$sum = new Decimal(0);
					foreach ($decimals as $decimal) {
						$sum = $sum->add($decimal);
					}
					return $sum;
				},
			],
			[
				'attribute' => 'created_at',
				'format' => 'date',
				'visible' => $this->withDates,
			],
			[
				'attribute' => 'updated_at',
				'format' => 'date',
				'visible' => $this->withDates,
			],
		];
	}

}

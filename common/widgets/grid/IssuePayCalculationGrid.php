<?php

namespace common\widgets\grid;

use common\models\issue\IssuePayCalculation;
use common\models\settlement\search\IssuePayCalculationSearch;
use common\widgets\GridView;
use Decimal\Decimal;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\Html;

class IssuePayCalculationGrid extends GridView {

	protected const ISSUE_COLUMN = IssueColumn::class;

	public $id = 'calculation-grid';

	public $showPageSummary = true;

	public bool $withIssue = true;
	public bool $withCustomer = true;
	public bool $withIssueType = true;
	public bool $withDates = true;

	public $userProvisionsId = null;

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
		$options = [];
		if ($model->isPayed()) {
			Html::addCssClass($options, 'payed-row success');
		} else {
			if ($model->isDelayed()) {
				Html::addCssClass($options, 'delayed-row warning');
			}
		}
		return $options;
	}

	protected function actionColumn(): array {
		return [
			'class' => ActionColumn::class,
		];
	}

	public function defaultColumns(): array {
		return [
			$this->actionColumn(),
			[
				'class' => static::ISSUE_COLUMN,
				'visible' => $this->withIssue,
			],
			[
				'class' => CustomerDataColumn::class,
				'visible' => $this->withCustomer,
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => IssuePayCalculationSearch::getTypesNames(),
			],
			[
				'attribute' => 'problem_status',
				'value' => 'problemStatusName',
				'filter' => IssuePayCalculationSearch::getProblemStatusesNames(),
				'visible' => $this->filterModel && ($this->filterModel->problem_status !== null || $this->filterModel->onlyWithProblems),
			],
			[
				'attribute' => 'owner_id',
				'value' => 'owner',
				'filter' => IssuePayCalculationSearch::getOwnerNames(),
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
				'filter' => IssuePayCalculationSearch::getStagesNames(),
			],
			[
				'class'=> CurrencyColumn::class,
				'pageSummary' => true,
				'attribute' => 'value',
			],
			[
				'class'=> CurrencyColumn::class,
				'attribute' => 'valueToPay',
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
				'class'=> CurrencyColumn::class,

				'attribute' => 'userProvisionsSum',
				'format' => 'currency',
				'visible' => $this->userProvisionsId !== null,
				'pageSummary' => true,
				'value' => function (IssuePayCalculation $model): ?Decimal {
					return $this->userProvisionsId === null
						? null
						: $model->getUserProvisionsSum($this->userProvisionsId);
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
				'noWrap' => true,
				'visible' => $this->withDates,
			],
			[
				'attribute' => 'updated_at',
				'format' => 'date',
				'noWrap' => true,
				'visible' => $this->withDates,
			],
		];
	}

}

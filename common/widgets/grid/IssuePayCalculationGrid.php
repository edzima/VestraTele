<?php

namespace common\widgets\grid;

use common\helpers\Html;
use common\models\issue\IssuePayCalculation;
use common\models\settlement\search\IssuePayCalculationSearch;
use common\widgets\GridView;
use Decimal\Decimal;
use Yii;
use yii\base\InvalidConfigException;

class IssuePayCalculationGrid extends GridView {

	/* @var IssuePayCalculationSearch */
	public $filterModel;

	public $id = 'calculation-grid';

	public $showPageSummary = true;

	public string $issueColumn = IssueColumn::class;
	public bool $withIssue = true;
	public bool $withOwner = true;
	public bool $withCustomer = true;
	public bool $withIssueType = true;
	public bool $withProblems = true;
	public bool $withDates = true;
	public bool $withStageOnCreate = true;
	public bool $withValueSummary = false;
	public bool $rowColors = true;

	public $userProvisionsId = null;

	public string $valueTypeIssueType = IssueTypeColumn::VALUE_SHORT;

	public bool $withAgent = true;

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
		if (empty($this->rowOptions) && $this->rowColors) {
			$this->rowOptions = static function (IssuePayCalculation $model): array {
				$options = Html::payStatusRowOptions($model);
				if ($model->hasProblemStatus()) {
					Html::addCssClass($options, 'problem-status-row danger');
				}
				return $options;
			};
		}
		parent::init();
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
				'class' => $this->issueColumn,
				'visible' => $this->withIssue,
			],
			[
				'class' => CustomerDataColumn::class,
				'visible' => $this->withCustomer,
			],
			[
				'class' => AgentDataColumn::class,
				'visible' => $this->withAgent,
				'value' => 'issue.agent.fullName',
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => IssuePayCalculationSearch::getTypesNames(),
			],
			[
				'attribute' => 'problem_status',
				'value' => 'problemStatusName',
				'filter' => $this->problemStatusFilter(),
				'visible' => $this->withProblems,
			],
			[
				'attribute' => 'owner_id',
				'value' => 'owner',
				'filter' => IssuePayCalculationSearch::getOwnerNames(),
				'visible' => $this->withOwner,
			],
			[
				'class' => IssueTypeColumn::class,
				'label' => Yii::t('backend', 'Issue type'),
				'attribute' => 'issue_type_id',
				'visible' => $this->withIssueType,
				'valueType' => $this->valueTypeIssueType,
			],
			/*
			[
				'attribute' => 'stage_id',
				'label' => Yii::t('backend', 'Issue stage on create'),
				'value' => 'stageName',
				'filter' => IssuePayCalculationSearch::getStagesNames(),
				'visible' => $this->withStageOnCreate,
			],
			*/
			[
				'class' => CurrencyColumn::class,
				'pageSummary' => $this->withValueSummary,
				'attribute' => 'value',
			],
			[
				'class' => CurrencyColumn::class,
				'attribute' => 'valueToPay',
				'pageSummary' => $this->withValueSummary,
				'pageSummaryFunc' => function (array $decimals): Decimal {
					$sum = new Decimal(0);
					foreach ($decimals as $decimal) {
						$sum = $sum->add($decimal);
					}
					return $sum;
				},
			],
			/*
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
			*/
			[
				'class' => CurrencyColumn::class,

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
				'value' => function (IssuePayCalculation $model): ?Decimal {
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
				'attribute' => 'updated_at',
				'format' => 'date',
				'noWrap' => true,
				'visible' => $this->withDates,
			],
		];
	}

	protected function problemStatusFilter(): array {
		return IssuePayCalculationSearch::getProblemStatusesNames();
	}

}

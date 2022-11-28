<?php

namespace common\widgets\grid;

use common\helpers\Html;
use common\models\issue\IssuePayCalculation;
use common\models\settlement\search\IssuePayCalculationSearch;
use common\widgets\GridView;
use Decimal\Decimal;
use kartik\select2\Select2;
use Yii;
use yii\base\InvalidConfigException;

class IssuePayCalculationGrid extends GridView {

	/* @var IssuePayCalculationSearch */
	public $filterModel;

	public $id = 'calculation-grid';

	public ?string $noteRoute = '/note/settlement';

	public $showPageSummary = true;

	public string $issueColumn = IssueColumn::class;
	public bool $withIssue = true;
	public bool $withOwner = true;
	public bool $withCustomer = true;
	public bool $withCaption = false;
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
		if ($this->withCaption && empty($this->caption)) {
			$this->caption = Yii::t('settlement', 'Settlements');
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
				'filter' => $this->filterModel ? $this->filterModel::getTypesNames() : null,
				'filterType' => static::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'multiple' => true,
						'placeholder' => Yii::t('common', 'Type'),
					],
					'size' => Select2::SIZE_SMALL,
					'showToggleAll' => false,
				],
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
				'label' => Yii::t('common', 'Issue type'),
				'attribute' => 'issue_type_id',
				'visible' => $this->withIssueType,
				'valueType' => $this->valueTypeIssueType,
			],
			[
				'label' => Yii::t('common', 'Stage'),
				'attribute' => 'issue_stage_id',
				'value' => 'issue.stage',
				'visible' => $this->filterModel ? $this->filterModel->withIssueStage : false,
				'filter' => $this->filterModel ? $this->filterModel::getIssueStagesNames() : null,
				'filterType' => static::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'multiple' => true,
						'placeholder' => Yii::t('common', 'Stage'),
					],
					'size' => Select2::SIZE_SMALL,
					'showToggleAll' => false,
				],
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
				'class' => CurrencyColumn::class,
				'pageSummary' => $this->withValueSummary,
				'attribute' => 'value',
				'contentBold' => false,
			],
			[
				'class' => CurrencyColumn::class,
				'attribute' => 'payedSum',
				'pageSummary' => $this->withValueSummary,
			],
			[
				'class' => CurrencyColumn::class,
				'attribute' => 'valueToPay',
				'pageSummary' => $this->withValueSummary,
			],
			[
				'class' => CurrencyColumn::class,
				'attribute' => 'userProvisionsSum',
				'contentBold' => false,
				'visible' => $this->userProvisionsId !== null,
				'pageSummary' => true,
				'value' => function (IssuePayCalculation $model): ?Decimal {
					return $this->userProvisionsId === null
						? null
						: $model->getUserProvisionsSum($this->userProvisionsId);
				},
			],
			[
				'class' => CurrencyColumn::class,
				'attribute' => 'userProvisionsSumNotPay',
				'visible' => $this->userProvisionsId !== null,
				'pageSummary' => true,
				'value' => function (IssuePayCalculation $model): ?Decimal {
					if ($this->userProvisionsId === null) {
						return new Decimal(0);
					}
					return $model->getUserProvisionsSumNotPay($this->userProvisionsId);
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
			$this->actionColumn(),
		];
	}

	protected function problemStatusFilter(): array {
		return IssuePayCalculationSearch::getProblemStatusesNames();
	}

}

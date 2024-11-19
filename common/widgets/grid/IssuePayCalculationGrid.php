<?php

namespace common\widgets\grid;

use common\assets\TooltipAsset;
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
	public bool $withDetails = true;
	public bool $withStageOnCreate = true;
	public bool $withValueSummary = false;
	public bool $rowColors = true;

	public $userProvisionsId = null;

	public string $valueTypeIssueType = IssueTypeColumn::VALUE_SHORT;

	public bool $withAgent = true;

	public $userId;
	public bool $userIsRequired = true;

	public const TYPE_PERCENTAGE = 'percentage';
	public const TYPE_VALUE = 'value';

	public string $type = self::TYPE_VALUE;

	public bool $filterType = false;

	public bool $detailsAsTitleTooltip = true;
	public bool $withIsPercentage = true;

	public function init(): void {
		if ($this->userId === null) {
			$this->userId = Yii::$app->user->getId();
		}
		if ($this->userIsRequired && empty($this->userId)) {
			throw new InvalidConfigException('UserId cannot be empty.');
		}
		if (!empty($this->id) && !isset($this->options['id'])) {
			$this->options['id'] = $this->id;
		}
		if ($this->filterModel !== null && !$this->filterModel instanceof IssuePayCalculationSearch) {
			throw new InvalidConfigException('$filter model must be instance of: ' . IssuePayCalculationSearch::class . '.');
		}
		if ($this->filterModel) {
			$this->withOwner = $this->filterModel->scenario !== IssuePayCalculationSearch::SCENARIO_OWNER;
			$this->type = $this->filterModel->is_percentage ? static::TYPE_PERCENTAGE : static::TYPE_VALUE;
		}
		if ($this->detailsAsTitleTooltip) {
			$this->withDetails = false;
		}

		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		if ($this->withCaption && empty($this->caption)) {
			$this->caption = $this->defaultCaption();
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

	protected function defaultCaption(): string {
		switch ($this->type) {
			case self::TYPE_VALUE:
				return Yii::t('settlement', 'Settlements ({currency})', [
					'currency' => Yii::$app->formatter->getCurrencySymbol(),
				]);
			case self::TYPE_PERCENTAGE:
				return Yii::t('settlement', 'Settlements (%)');
			default:
				return Yii::t('settlement', 'Settlements');
		}
	}

	protected function actionColumn(): array {
		return [
			'class' => ActionColumn::class,
		];
	}

	public function defaultColumns(): array {
		return [
			[
				'attribute' => 'is_percentage',
				'value' => 'type.is_percentage',
				'format' => 'boolean',
				'label' => Yii::t('settlement', '%'),
				'visible' => $this->withIsPercentage,
			],
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
				'attribute' => 'type_id',
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
				'value' => function (IssuePayCalculation $model): string {
					$value = $model->getTypeName();
					if (!empty($model->details)) {
						$this->registerTooltipAssets();
						return Html::tag('span', $value, [
							TooltipAsset::DEFAULT_ATTRIBUTE_NAME => Html::encode($model->details),
						]);
					}
					return $value;
				},
				'format' => 'raw',
			],
			[
				'attribute' => 'details',
				'visible' => $this->withDetails,
			],
			[
				'attribute' => 'problem_status',
				'value' => 'problemStatusName',
				'filter' => $this->problemStatusFilter(),
				'visible' => $this->withProblems,
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
				'pageSummary' => $this->withValueSummary,
				'attribute' => 'value',
				'format' => 'percent',
				'label' => Yii::t('settlement', 'Value'),
				'visible' => $this->isPercentageType(),
			],
			[
				'class' => CurrencyColumn::class,
				'pageSummary' => $this->withValueSummary,
				'attribute' => 'value',
				'contentBold' => false,
				'visible' => $this->isValueType(),
			],
			[
				'attribute' => 'payedSum',
				'pageSummary' => $this->withValueSummary,
				'label' => Yii::t('settlement', 'Settled Percent value'),
				'format' => 'percent',
				'pageSummaryFuncAsDecimal' => true,
				'visible' => $this->isPercentageType(),
			],
			[
				'class' => CurrencyColumn::class,
				'attribute' => 'payedSum',
				'pageSummary' => $this->withValueSummary,
				'visible' => $this->isValueType(),
			],
			[
				'attribute' => 'valueToPay',
				'format' => 'percent',
				'pageSummaryFuncAsDecimal' => true,
				'label' => Yii::t('settlement', 'Percent value to Settle'),
				'pageSummary' => $this->withValueSummary,
				'visible' => $this->isPercentageType(),
			],
			[
				'class' => CurrencyColumn::class,
				'attribute' => 'valueToPay',
				'pageSummary' => $this->withValueSummary,
				'visible' => $this->isValueType(),
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
				'attribute' => 'owner_id',
				'value' => 'owner',
				'filter' => IssuePayCalculationSearch::getOwnerNames(),
				'visible' => $this->withOwner,
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

	protected function isValueType(): bool {
		return $this->type === static::TYPE_VALUE;
	}

	protected function isPercentageType(): bool {
		return $this->type === static::TYPE_PERCENTAGE;
	}

	protected function problemStatusFilter(): array {
		return IssuePayCalculationSearch::getProblemStatusesNames();
	}

	private function registerTooltipAssets(): void {
		$this->view->registerJs(TooltipAsset::initScript());
	}

}

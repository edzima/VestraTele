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
		if (!empty($this->id) && !isset($this->options['id'])) {
			$this->options['id'] = $this->id;
		}
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
			],
			[
				'class' => IssueColumn::class,
				'visible' => $this->withIssue,
			],
			[
				'attribute' => 'problem_status',
				'value' => 'problemStatusName',
				'filter' => $this->filterModel::getProblemStatusesNames(),
				'visible' => $this->filterModel->problem_status !== null || $this->filterModel->onlyWithProblems,
			],
			[
				'class' => IssueTypeColumn::class,
				'label' => Yii::t('backend', 'Issue type'),
				'attribute' => 'issue_type_id',
			],
			[
				'attribute' => 'stage_id',
				'label' => Yii::t('backend', 'Issue stage on create'),
				'value' => 'stage.name',
				'filter' => $this->filterModel::getStagesNames(),
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => $this->filterModel::getTypesNames(),
			],
			[
				'class' => CustomerDataColumn::class,
				'visible' => $this->withCustomer,
			],

			[
				'attribute' => 'value',
				'format' => 'currency',
			],
			[
				'attribute' => 'valueToPay',
				'format' => 'currency',
				'visible' => function (IssuePayCalculation $model): bool {
					return !$model->isPayed();
				},
			],
			[
				'attribute' => 'providerName',
				'filter' => $this->filterModel::getProvidersTypesNames(),
				'value' => function (IssuePayCalculation $model): string {
					if ($model->provider_type === IssuePayCalculation::PROVIDER_RESPONSIBLE_ENTITY) {
						return $model->getProviderName();
					}
					return $this->filterModel::getProvidersTypesNames()[IssuePayCalculation::PROVIDER_CLIENT];
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
		];
	}

}

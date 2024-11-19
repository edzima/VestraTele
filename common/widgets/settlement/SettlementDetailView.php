<?php

namespace common\widgets\settlement;

use backend\modules\settlement\Module;
use common\helpers\Html;
use common\models\issue\IssuePayCalculation;
use Decimal\Decimal;
use Yii;
use yii\base\InvalidConfigException;
use yii\widgets\DetailView;

class SettlementDetailView extends DetailView {

	/** @var IssuePayCalculation */
	public $model;

	public ?int $userIdProvisions = null;

	public bool $withType = true;

	public ?string $typeRoute = null;
	public bool $withOwner = true;

	public bool $withDetails = true;

	public bool $withCreatedAt = false;
	public bool $withValueWithoutCosts = false;

	public function init(): void {
		if (!$this->model instanceof IssuePayCalculation) {
			throw new InvalidConfigException('$model must be instance of: ' . IssuePayCalculation::class);
		}
		if (!YII_IS_FRONTEND && Yii::$app->user->can(Module::ROLE_SETTLEMENT_TYPE_MANAGER)) {
			$this->typeRoute = '/settlement/type/view';
		}
		if (empty($this->attributes)) {
			$this->attributes = $this->defaultAttributes();
		}
		parent::init();
	}

	protected function defaultAttributes(): array {
		return [
			[
				'attribute' => 'problemStatusName',
				'visible' => $this->model->hasProblemStatus() && !$this->model->isProvisionControl(),
			],
			[
				'label' => Yii::t('settlement', 'Provision Control'),
				'format' => 'boolean',
				'value' => $this->model->isProvisionControl(),
				'visible' => $this->model->isProvisionControl(),
			],
			[
				'attribute' => 'typeName',
				'value' => function (IssuePayCalculation $model): string {
					if ($this->typeRoute) {
						return Html::a($model->getTypeName(), [
							$this->typeRoute,
							'id' => $model->type_id,
						]);
					}
					return $model->getTypeName();
				},
				'format' => 'html',
				'visible' => $this->withType,
			],
			[
				'attribute' => 'details',
				'visible' => $this->withDetails && !empty($this->model->details),
			],
			[
				'attribute' => 'stageName',
				'label' => Yii::t('settlement', 'Stage on create'),
				'visible' => $this->model->stage_id !== $this->model->issue->stage_id,
			],
			[
				'attribute' => 'issue.type',
				'label' => Yii::t('common', 'Issue type'),
			],
			[
				'attribute' => 'issue.stage',
				'label' => Yii::t('common', 'Issue stage'),
			],
			[
				'attribute' => 'providerName',
			],
			[
				'attribute' => 'owner',
				'visible' => $this->withOwner,
			],

			[
				'attribute' => 'value',
				'format' => $this->valueFormat(),
			],
			[
				'attribute' => 'costsSum',
				'format' => 'currency',
				'visible' => $this->model->hasCosts,
			],
			[
				'attribute' => 'valueWithoutCosts',
				'format' => $this->valueFormat(),
				'visible' => $this->model->hasCosts,
			],
			[
				'attribute' => 'valueToPay',
				'format' => $this->valueFormat(),
				'visible' => !$this->model->isPayed(),
			],
			[
				'attribute' => 'userProvisionsSum',
				'visible' => $this->userIdProvisions !== null,
				'value' => function (IssuePayCalculation $model): ?Decimal {
					return $model->getUserProvisionsSum($this->userIdProvisions);
				},
				'format' => 'currency',
			],
			[
				'attribute' => 'userProvisionsSumNotPay',
				'format' => 'currency',
				'visible' => $this->userIdProvisions !== null && $this->model->getUserProvisionsSum($this->userIdProvisions) > 0,
				'value' => function (IssuePayCalculation $model): Decimal {
					return $model->getUserProvisionsSumNotPay($this->userIdProvisions);
				},
			],
			[
				'attribute' => 'created_at',
				'visible' => $this->withCreatedAt,
				'format' => 'date',
			],
		];
	}

	protected function valueFormat(): string {
		return $this->model->isPercentageType()
			? 'percent'
			: 'currency';
	}

}

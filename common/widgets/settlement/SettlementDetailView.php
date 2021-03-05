<?php

namespace common\widgets\settlement;

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
	public bool $withOwner = true;

	public bool $withValueWithoutCosts = false;

	public function init(): void {
		if (!$this->model instanceof IssuePayCalculation) {
			throw new InvalidConfigException('$model must be instance of: ' . IssuePayCalculation::class);
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
				'visible' => $this->model->hasProblemStatus(),
			],
			[
				'attribute' => 'typeName',
				'visible' => $this->withType,
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
				'format' => 'currency',
			],
			[
				'attribute' => 'valueToPay',
				'format' => 'currency',
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
		];
	}

}

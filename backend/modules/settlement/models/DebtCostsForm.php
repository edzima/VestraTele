<?php

namespace backend\modules\settlement\models;

use common\models\issue\IssueCost;
use Yii;

class DebtCostsForm extends IssueCostForm {

	public string $pccPercent = '1';
	public string $pit4Percent = '17';

	public function init() {
		parent::init();
		$this->user_id = $this->getIssue()->getIssueModel()->customer->getId();
		$this->date_at = $this->getIssue()->getIssueModel()->signing_at;
	}

	public function rules(): array {
		return array_merge(parent::rules(), [
			[['!user_id', 'settled_at', 'pay_type'], 'required'],
			[['pccPercent', 'pit4Percent'], 'required'],
			[['pccPercent', 'pit4Percent'], 'number', 'min' => 0],
			[['pccPercent', 'pit4Percent'], 'number', 'max' => 100],
		]);
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'pccPercent' => Yii::t('settlement', 'PCC (%)'),
			'pit4Percent' => Yii::t('settlement', 'PIT-4 (%)'),
		]);
	}

	public function save(): bool {
		$this->type = IssueCost::TYPE_PURCHASE_OF_RECEIVABLES;
		if (!parent::save()) {
			return false;
		}
		$costs = [];
		$costs[] = $this->createPCCCost();
		$costs[] = $this->createPIT4Cost();
		foreach ($costs as $cost) {
			$cost->save();
		}
		return true;
	}

	protected function createPurchaseOfReceivablesCost(): IssueCost {
		$cost = $this->createCost(IssueCost::TYPE_PURCHASE_OF_RECEIVABLES);
		$cost->value = $this->value;
		$cost->settled_at = $this->settled_at;
		return $cost;
	}

	protected function createPCCCost(): IssueCost {
		$cost = $this->createCost(IssueCost::TYPE_PCC);
		$cost->value = $this->getValue()->mul($this->pccPercent)->div(100)->toFixed(2);
		return $cost;
	}

	protected function createPIT4Cost(): IssueCost {
		$cost = $this->createCost(IssueCost::TYPE_PIT_4);
		$cost->value = $this->getValue()->mul($this->pit4Percent)->div(100)->toFixed(2);
		$cost->date_at = $this->settled_at;
		return $cost;
	}

	protected function createCost(string $type): IssueCost {
		$cost = new IssueCost();
		$cost->type = $type;
		$cost->date_at = $this->date_at;
		$cost->issue_id = $this->getIssue()->getIssueId();
		$cost->user_id = $this->user_id;
		return $cost;
	}

	public function isVisibleField(string $attribute): bool {
		$attributes = [
			'pay_type',
			'settled_at',
			'value',
		];
		return in_array($attribute, $attributes);
	}

}

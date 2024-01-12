<?php

namespace backend\modules\settlement\models;

use common\helpers\DateTimeHelper;
use common\models\issue\IssueCost;
use common\models\issue\IssueCostInterface;
use DateTime;
use Decimal\Decimal;
use Yii;

class DebtCostsForm extends IssueCostForm {

	public int $minPccValue = 1000;
	public string $pccPercent = '1';
	public string $pit4Percent = '17';

	public function init() {
		parent::init();
		$this->user_id = $this->getIssue()->getIssueModel()->customer->getId();
		$this->date_at = $this->getIssue()->getIssueModel()->signing_at;
	}

	public function rules(): array {
		return array_merge(parent::rules(), [
			[['!user_id', 'settled_at', 'transfer_type', 'base_value', 'pccPercent', 'pit4Percent'], 'required'],
			[
				'base_value', 'compare', 'compareAttribute' => 'value', 'operator' => '>=',
				'enableClientValidation' => false,
			],
			[['pccPercent', 'pit4Percent'], 'number', 'min' => 0],
			[['pccPercent', 'pit4Percent'], 'number', 'max' => 100],
		]);
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'pccPercent' => Yii::t('settlement', 'PCC (%)'),
			'pit4Percent' => Yii::t('settlement', 'PIT-4 (%)'),
			'value' => Yii::t('settlement', 'Value of purchase'),
			'base_value' => Yii::t('settlement', 'Nominal Value'),
		]);
	}

	public function save(bool $validate = true): bool {
		$this->type = IssueCostInterface::TYPE_PURCHASE_OF_RECEIVABLES;
		if (!parent::save($validate)) {
			return false;
		}
		$costs = [];
		$pcc = $this->createPCCCost();
		if ($pcc) {
			$costs[] = $pcc;
		}
		$costs[] = $this->createPIT4Cost();
		foreach ($costs as $cost) {
			$cost->save();
		}
		return true;
	}

	protected function createPCCCost(): ?IssueCost {
		$value = $this->getBaseValue();
		if ($value < $this->minPccValue) {
			return null;
		}
		$cost = $this->createCost(IssueCostInterface::TYPE_PCC);
		$cost->base_value = $value->toFixed(2);
		$cost->value = $value->mul($this->pccPercent)->div(100)->toFixed();
		$cost->deadline_at = date('Y-m-d', strtotime($cost->date_at . ' + 14 days'));
		return $cost;
	}

	protected function createPIT4Cost(): IssueCost {
		$cost = $this->createCost(IssueCostInterface::TYPE_PIT_4);
		$cost->base_value = $this->getValue()->toFixed(2);
		$cost->value = $this->getValue()->mul($this->pit4Percent)->div(100)->toFixed();
		$cost->date_at = $this->settled_at;
		$cost->deadline_at = DateTimeHelper::getSameDayNextMonth(new DateTime($cost->date_at))->format('Y-m-20');
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
			'transfer_type',
			'settled_at',
			'base_value',
			'value',
		];
		return in_array($attribute, $attributes);
	}

	public function getBaseValue(): Decimal {
		return new Decimal($this->base_value);
	}

}

<?php

namespace backend\tests\unit\settlement;

use backend\modules\settlement\models\DebtCostsForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssueCost;
use common\tests\_support\UnitModelTrait;

class DebtCostsFormTest extends Unit {

	use UnitModelTrait;

	private DebtCostsForm $model;

	public function _before() {
		parent::_before();
		$this->model = new DebtCostsForm(
			$this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0)
		);
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::customer(),
			SettlementFixtureHelper::cost(false)
		);
	}

	public function testEmpty(): void {
		$this->model->pccPercent = '';
		$this->model->pit4Percent = '';
		$this->model->date_at = '';
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Value cannot be blank.', 'value');
		$this->thenSeeError('Date at cannot be blank.', 'date_at');
		$this->thenSeeError('Settled at cannot be blank.', 'settled_at');
		$this->thenSeeError('Pay Type cannot be blank.', 'pay_type');
		$this->thenSeeError('PCC (%) cannot be blank.', 'pccPercent');
		$this->thenSeeError('PIT-4 (%) cannot be blank.', 'pit4Percent');
	}

	public function testValid(): void {
		$this->model->pccPercent = 1;
		$this->model->pit4Percent = 17;
		$this->model->value = 1000;
		$this->model->settled_at = '2020-01-01';
		$this->model->pay_type = IssueCost::PAY_TYPE_CASH;
		$this->thenSuccessSave();
		$this->thenSeeCost(IssueCost::TYPE_PURCHASE_OF_RECEIVABLES, [
			'value' => 1000,
			'date_at' => $this->model->getIssue()->getIssueModel()->signing_at,
			'settled_at' => '2020-01-01',
		]);
		$this->thenSeeCost(IssueCost::TYPE_PCC, [
			'value' => 10,
			'date_at' => $this->model->getIssue()->getIssueModel()->signing_at,
		]);
		$this->thenSeeCost(IssueCost::TYPE_PIT_4, [
			'value' => 170,
			'date_at' => '2020-01-01',
		]);
	}

	private function thenSeeCost(string $type, array $attributes): void {
		$attributes['type'] = $type;
		$attributes['issue_id'] = $this->model->getIssue()->getIssueId();
		$attributes['user_id'] = $this->model->getIssue()->getIssueModel()->customer->id;
		$this->tester->seeRecord(IssueCost::class, $attributes);
	}

	public function getModel(): DebtCostsForm {
		return $this->model;
	}
}

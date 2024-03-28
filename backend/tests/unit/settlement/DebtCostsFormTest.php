<?php

namespace backend\tests\unit\settlement;

use backend\modules\settlement\models\DebtCostsForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueCost;
use common\models\issue\IssueCostInterface;
use common\models\settlement\TransferType;
use common\tests\_support\UnitModelTrait;

class DebtCostsFormTest extends Unit {

	use UnitModelTrait;

	private DebtCostsForm $model;

	private const ISSUE_SIGNED_AT = '2020-01-01';
	protected const PCC_PERCENT = 1;
	protected const PIT_PERCENT = 17;

	public function _before() {
		parent::_before();
		$this->giveModel();
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
		$this->model->transfer_type = null;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Nominal Value cannot be blank.', 'base_value');
		$this->thenSeeError('Value of purchase cannot be blank.', 'value');
		$this->thenSeeError('Date at cannot be blank.', 'date_at');
		$this->thenSeeError('Settled at cannot be blank.', 'settled_at');
		$this->thenSeeError('Transfer Type cannot be blank.', 'transfer_type');
		$this->thenSeeError('PCC (%) cannot be blank.', 'pccPercent');
		$this->thenSeeError('PIT-4 (%) cannot be blank.', 'pit4Percent');
	}

	public function testValid(): void {
		$this->model->value = 1000;
		$this->model->base_value = 1500;
		$this->model->settled_at = '2020-01-01';
		$this->thenSuccessSave();
		$this->thenSeeCost(IssueCostInterface::TYPE_PURCHASE_OF_RECEIVABLES, 1000, [
			'transfer_type' => $this->model->transfer_type,
			'date_at' => static::ISSUE_SIGNED_AT,
			'settled_at' => '2020-01-01',
		]);
		$this->thenSeeCost(IssueCostInterface::TYPE_PCC, 15, [
			'transfer_type' => null,
		]);
		$this->thenSeeCost(IssueCostInterface::TYPE_PIT_4, 170, [
			'transfer_type' => null,
			'date_at' => '2020-01-01',
		]);
	}

	public function testPCCAndPITRound(): void {
		$this->model->value = 9999;
		$this->model->base_value = 9999;
		$this->model->settled_at = '2020-01-01';
		$this->thenSuccessSave();
		$this->thenDontSeeCost(IssueCostInterface::TYPE_PCC, '99,99');
		$this->thenSeeCost(IssueCostInterface::TYPE_PCC, 100);

		$this->thenDontSeeCost(IssueCostInterface::TYPE_PIT_4, '1699,83');
		$this->thenSeeCost(IssueCostInterface::TYPE_PIT_4, 1700);
	}

	public function testBaseValueLessThanPCCMinLimit(): void {
		$this->model->value = 900;
		$this->model->base_value = 999;
		$this->model->settled_at = '2020-01-01';
		$this->thenSuccessSave();
		$this->thenSeeCost(IssueCostInterface::TYPE_PURCHASE_OF_RECEIVABLES, 900);
		$this->thenDontSeeCost(IssueCostInterface::TYPE_PCC, '10');
		$this->thenSeeCost(IssueCostInterface::TYPE_PIT_4, '153');
	}

	public function testPCCValues(): void {
		$this->model->value = 1000;
		$this->model->base_value = 1500;
		$this->model->settled_at = '2020-01-01';
		$this->thenSuccessSave();
		$this->thenSeeCost(IssueCostInterface::TYPE_PCC, 15, [
			'base_value' => 1500,
			'date_at' => '2020-01-01',
			'deadline_at' => ' 2020-01-15',
		]);
	}

	public function testPIT4Values(): void {
		$this->model->value = 1000;
		$this->model->base_value = 1500;
		$this->model->settled_at = '2020-01-01';
		$this->thenSuccessSave();
		$this->thenSeeCost(IssueCostInterface::TYPE_PIT_4, 170, [
			'base_value' => 1000,
			'date_at' => '2020-01-01',
			'deadline_at' => ' 2020-02-20',
		]);
	}

	private function thenSeeCost(string $type, string $value, array $attributes = []): void {
		$this->tester->seeRecord(IssueCost::class, array_merge($attributes, $this->costAttributes($type, $value)));
	}

	private function thenDontSeeCost(string $type, string $value, array $attributes = []): void {
		$this->tester->dontSeeRecord(IssueCost::class, array_merge($attributes, $this->costAttributes($type, $value)));
	}

	private function costAttributes(string $type, string $value): array {
		$attributes = [];
		$attributes['type'] = $type;
		$attributes['value'] = $value;
		$attributes['issue_id'] = $this->model->getIssue()->getIssueId();
		$attributes['user_id'] = $this->model->getIssue()->getIssueModel()->customer->id;
		return $attributes;
	}

	public function getModel(): DebtCostsForm {
		return $this->model;
	}

	private function giveModel(): void {
		$this->model = new DebtCostsForm();
		$this->model->setIssue($this->getIssue());
		$this->model->pccPercent = static::PCC_PERCENT;
		$this->model->pit4Percent = static::PIT_PERCENT;
		$this->model->transfer_type = TransferType::TRANSFER_TYPE_BANK;
	}

	protected function getIssue(): Issue {
		/**
		 * @var Issue $issue
		 */
		$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$issue->signing_at = static::ISSUE_SIGNED_AT;
		return $issue;
	}
}

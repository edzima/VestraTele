<?php

namespace backend\tests\unit\settlement;

use backend\modules\settlement\models\IssueCostForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueCost;
use common\tests\_support\UnitModelTrait;
use yii\base\Model;

class IssueCostFormTest extends Unit {

	use UnitModelTrait;

	private IssueCostForm $model;

	public function _before() {
		parent::_before();
		$this->tester->haveFixtures(
			array_merge(
				IssueFixtureHelper::fixtures(),
				SettlementFixtureHelper::cost(false),
			));
		$this->model = new IssueCostForm();
		$this->model->setIssue($this->grabIssue());
	}

	public function testEmpty(): void {
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Type cannot be blank.', 'type');
		$this->thenSeeError('Value cannot be blank.', 'value');
		$this->thenSeeError('Date at cannot be blank.', 'date_at');
	}

	public function testWithoutIssue() {
		$this->model = new IssueCostForm();
		$model = $this->model;
		$model->type = IssueCost::TYPE_PURCHASE_OF_RECEIVABLES;
		$model->date_at = '2020-01-01';
		$model->value = 600;
		$model->vat = 23;
		$this->thenSuccessSave();
		$this->tester->seeRecord(IssueCost::class, [
			'issue_id' => null,
			'type' => IssueCost::TYPE_PURCHASE_OF_RECEIVABLES,
			'value' => 600,
			'vat' => 23,
			'date_at' => '2020-01-01',
		]);
	}

	public function testValid(): void {
		$model = $this->model;
		$model->type = IssueCost::TYPE_PURCHASE_OF_RECEIVABLES;
		$model->date_at = '2020-01-01';
		$model->value = 600;
		$model->vat = 23;
		$this->thenSuccessSave();
		$this->tester->seeRecord(IssueCost::class, [
			'issue_id' => $model->getIssue()->getIssueId(),
			'type' => IssueCost::TYPE_PURCHASE_OF_RECEIVABLES,
			'value' => 600,
			'vat' => 23,
			'date_at' => '2020-01-01',
		]);
	}

	public function testSettledAtSmallerThanDate(): void {
		$model = $this->model;
		$model->type = IssueCost::TYPE_PURCHASE_OF_RECEIVABLES;
		$model->date_at = '2020-01-01';
		$model->settled_at = '2019-02-02';
		$model->value = 600;
		$model->vat = 23;
		$this->thenUnsuccessSave();
		$this->thenSeeError('Settled at must be greater than or equal to "Date at".', 'settled_at');
		$this->tester->dontSeeRecord(IssueCost::class, [
			'issue_id' => $model->getIssue()->getIssueId(),
			'type' => IssueCost::TYPE_PURCHASE_OF_RECEIVABLES,
			'value' => 600,
			'vat' => 23,
			'date_at' => '2020-01-01',
			'settled_at' => '2019-02-02',
		]);
	}

	public function testSettledAt(): void {
		$model = $this->model;
		$model->type = IssueCost::TYPE_PURCHASE_OF_RECEIVABLES;
		$model->date_at = '2020-01-01';
		$model->settled_at = '2020-02-02';
		$model->value = 600;
		$model->vat = 23;
		$this->thenSuccessSave();
		$this->tester->seeRecord(IssueCost::class, [
			'issue_id' => $model->getIssue()->getIssueId(),
			'type' => IssueCost::TYPE_PURCHASE_OF_RECEIVABLES,
			'value' => 600,
			'vat' => 23,
			'date_at' => '2020-01-01',
			'settled_at' => '2020-02-02',
		]);
	}

	public function testInstallmentWithoutUser(): void {
		$model = $this->model;
		$model->type = IssueCost::TYPE_INSTALLMENT;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('User cannot be blank.', 'user_id');
	}

	public function testInstallmentWithIssueUser(): void {
		$model = $this->model;
		$model->type = IssueCost::TYPE_INSTALLMENT;
		$model->user_id = $model->getIssue()->getIssueModel()->agent->id;
		$model->date_at = '2020-01-01';
		$model->value = 150;
		$model->vat = 23;
		$this->thenSuccessSave();
		$this->tester->seeRecord(IssueCost::class, [
			'issue_id' => $model->getIssue()->getIssueId(),
			'type' => IssueCost::TYPE_INSTALLMENT,
			'user_id' => $model->getIssue()->getIssueModel()->agent->id,
			'value' => 150,
			'vat' => 23,
			'date_at' => '2020-01-01',
		]);
	}

	public function testInstallmentWithNotIssueUser(): void {
		$model = $this->model;
		$model->type = IssueCost::TYPE_INSTALLMENT;
		$model->user_id = UserFixtureHelper::AGENT_EMILY_PAT;
		$model->date_at = '2020-01-01';
		$model->value = 150;
		$model->vat = 0;
		$this->thenUnsuccessSave();
		$this->thenSeeError('User must be from issue users.', 'user_id');
		$this->tester->dontSeeRecord(IssueCost::class, [
			'issue_id' => $model->getIssue()->id,
			'type' => IssueCost::TYPE_INSTALLMENT,
			'user_id' => UserFixtureHelper::AGENT_EMILY_PAT,
			'value' => 150,
			'vat' => 0,
			'date_at' => '2020-01-01',
		]);
	}

	public function testInvalidType(): void {
		$model = $this->model;
		$model->type = 'invalid-type';
		$model->date_at = '2020-01-01';
		$model->value = 600;
		$model->vat = 23;
		$this->thenUnsuccessSave();
		$this->thenSeeError('Type is invalid.', 'type');
	}

	public function testInvalidPayType(): void {
		$model = $this->model;
		$model->transfer_type = 'invalid-pat-type';
		$model->date_at = '2020-01-01';
		$model->value = 600;
		$model->vat = 23;
		$this->thenUnsuccessSave();
		$this->thenSeeError('Transfer Type is invalid.', 'transfer_type');
	}

	public function testWithPayType(): void {
		$model = $this->model;
		$model->type = IssueCost::TYPE_OFFICE;
		$model->transfer_type = IssueCost::TRANSFER_TYPE_CASH;
		$model->date_at = '2020-01-01';
		$model->value = 600;
		$model->vat = 23;
		$this->thenSuccessSave();
		$this->tester->seeRecord(IssueCost::class, [
			'type' => IssueCost::TYPE_OFFICE,
			'transfer_type' => IssueCost::TRANSFER_TYPE_CASH,
			'issue_id' => $this->model->getIssue()->getIssueId(),
			'value' => 600,
			'vat' => 23,
		]);
	}

	public function testUpdate(): void {
		$this->model = IssueCostForm::createFromModel($this->tester->grabFixture(SettlementFixtureHelper::COST, '0'));
		$model = $this->model;
		$this->tester->assertNotSame('10000', $model->value);
		$model->value = 10000;
		$this->thenSuccessSave();
		$attributes = $model->getModel()->getAttributes();
		$attributes['value'] = 10000;
		$this->tester->seeRecord(IssueCost::class, $attributes);
	}

	protected function grabIssue(int $index = 0): Issue {
		return $this->tester->grabFixture(IssueFixtureHelper::ISSUE, $index);
	}

	public function getModel(): Model {
		return $this->model;
	}
}

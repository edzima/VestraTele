<?php

namespace backend\tests\unit\settlement;

use backend\modules\settlement\models\IssueCostForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\fixtures\settlement\CostFixture;
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
			array_merge(IssueFixtureHelper::fixtures(),
				[
					'cost' => [
						'class' => CostFixture::class,
						'dataFile' => IssueFixtureHelper::dataDir() . 'issue/cost.php',
					],
				]));
		$this->model = new IssueCostForm($this->grabIssue());
	}

	public function testEmpty(): void {
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Type cannot be blank.', 'type');
		$this->thenSeeError('Value with VAT cannot be blank.', 'value');
		$this->thenSeeError('VAT (%) cannot be blank.', 'vat');
		$this->thenSeeError('Date at cannot be blank.', 'date_at');
	}

	public function testValid(): void {
		$model = $this->model;
		$model->type = IssueCost::TYPE_PURCHASE_OF_RECEIVABLES;
		$model->date_at = '2020-01-01';
		$model->value = 600;
		$model->vat = 23;
		$this->thenSuccessSave();
		$this->tester->seeRecord(IssueCost::class, [
			'issue_id' => $model->getIssue()->id,
			'type' => IssueCost::TYPE_PURCHASE_OF_RECEIVABLES,
			'value' => 600,
			'vat' => 23,
			'date_at' => '2020-01-01',
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
		$model->user_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$model->date_at = '2020-01-01';
		$model->value = 150;
		$model->vat = 0;
		$this->thenSuccessSave();
		$this->tester->seeRecord(IssueCost::class, [
			'issue_id' => $model->getIssue()->id,
			'type' => IssueCost::TYPE_INSTALLMENT,
			'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'value' => 150,
			'vat' => 0,
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
		$this->tester->assertFalse($model->save());
		$this->tester->dontSeeRecord(IssueCost::class, [
			'issue_id' => $model->getIssue()->id,
			'type' => 'invalid-type',
			'value' => 600,
			'vat' => 23,
			'date_at' => '2020-01-01',
		]);
	}

	protected function grabIssue(int $index = 0): Issue {
		return $this->tester->grabFixture(IssueFixtureHelper::ISSUE, $index);
	}

	public function getModel(): Model {
		return $this->model;
	}
}

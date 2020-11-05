<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\IssueCostForm;
use backend\tests\fixtures\IssueFixtureHelper;
use backend\tests\unit\Unit;
use common\fixtures\issue\CostFixture;
use common\models\issue\Issue;
use common\models\issue\IssueCost;

class IssueCostFormTest extends Unit {

	public function _before() {
		parent::_before();
		$this->tester->haveFixtures(
			array_merge(IssueFixtureHelper::fixtures(),
				[
					'cost' => [
						'class' => CostFixture::class,
						'dataFile' => codecept_data_dir() . 'issue/cost.php',
					],
				]));
	}

	public function testEmpty(): void {
		$model = new IssueCostForm($this->grabIssue());
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Type cannot be blank.', $model->getFirstError('type'));
		$this->tester->assertSame('Value with VAT cannot be blank.', $model->getFirstError('value'));
		$this->tester->assertSame('VAT (%) cannot be blank.', $model->getFirstError('vat'));
		$this->tester->assertSame('Date at cannot be blank.', $model->getFirstError('date_at'));
	}

	public function testValid(): void {
		$model = new IssueCostForm($this->grabIssue());
		$model->type = IssueCost::TYPE_PURCHASE_OF_RECEIVABLES;
		$model->date_at = '2020-01-01';
		$model->value = 600;
		$model->vat = 23;
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(IssueCost::class, [
			'issue_id' => $model->getIssue()->id,
			'type' => IssueCost::TYPE_PURCHASE_OF_RECEIVABLES,
			'value' => 600,
			'vat' => 23,
			'date_at' => '2020-01-01',
		]);
	}

	public function testInvalidType(): void {
		$model = new IssueCostForm($this->grabIssue());
		$model->type = 'invalid-type';
		$model->date_at = '2020-01-01';
		$model->value = 600;
		$model->vat = 23;
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
		return $this->tester->grabFixture('issue', $index);
	}
}

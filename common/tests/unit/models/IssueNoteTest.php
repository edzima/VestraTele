<?php

namespace common\tests\unit\models;

use common\models\issue\IssueNote;
use common\tests\unit\Unit;

class IssueNoteTest extends Unit {

	public function testEmpty(): void {
		$model = new IssueNote();
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Issue ID cannot be blank.', $model->getFirstError('issue_id'));
		$this->tester->assertSame('User ID cannot be blank.', $model->getFirstError('user_id'));
		$this->tester->assertSame('Title cannot be blank.', $model->getFirstError('title'));
		$this->tester->assertSame('Description cannot be blank.', $model->getFirstError('description'));
	}

	public function testType(): void {
		$model = new IssueNote();
		$this->tester->assertFalse($model->isForSettlement());
		$this->tester->assertFalse($model->isForSummon());
		$model->type = IssueNote::generateType(IssueNote::TYPE_SETTLEMENT, 1);
		$this->tester->assertTrue($model->isForSettlement());
		$this->tester->assertFalse($model->isForSummon());
		$model->type = IssueNote::generateType(IssueNote::TYPE_SUMMON, 1);
		$this->tester->assertFalse($model->isForSettlement());
		$this->tester->assertTrue($model->isForSummon());
	}
}

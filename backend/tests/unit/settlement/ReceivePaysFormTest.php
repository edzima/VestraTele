<?php

namespace backend\tests\unit\settlement;

use backend\modules\settlement\models\ReceivePaysForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;

class ReceivePaysFormTest extends Unit {

	public function _before(): void {
		parent::_before();
		$this->tester->haveFixtures(
			array_merge(
				IssueFixtureHelper::fixtures(),
				IssueFixtureHelper::settlements(),
				IssueFixtureHelper::payReceived()
			)
		);
	}

	public function testEmpty(): void {
		$model = new  ReceivePaysForm();
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Receiver cannot be blank.', $model->getFirstError('user_id'));
		$this->tester->assertSame('Pays cannot be blank.', $model->getFirstError('pays_ids'));
		$this->tester->assertSame('Date cannot be blank.', $model->getFirstError('date'));
	}

	public function testGetPaysWithoutUser(): void {
		$model = new  ReceivePaysForm();
		$this->assertEmpty($model->getNotTransferPays());
	}

	public function testGetPaysForValidUser(): void {
		$model = new  ReceivePaysForm();
		$model->user_id = 300;
		$this->assertCount(1, $model->getNotTransferPays());
		$model->user_id = 301;
		$this->assertCount(1, $model->getNotTransferPays());
		$model->user_id = 302;
		$this->assertCount(2, $model->getNotTransferPays());
	}

	public function testAlreadyTransferPay(): void {
		$model = new  ReceivePaysForm();
		$model->user_id = 300;
		$model->pays_ids = [2];
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Pays is invalid.', $model->getFirstError('pays_ids'));
	}

	public function testOtherUserPay(): void {
		$model = new  ReceivePaysForm();
		$model->user_id = 300;
		$model->pays_ids = [3];
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Pays is invalid.', $model->getFirstError('pays_ids'));
	}

}

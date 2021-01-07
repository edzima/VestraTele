<?php

namespace common\tests\unit\settlement;

use common\models\settlement\PayInterface;
use common\models\settlement\PaysForm;
use Decimal\Decimal;

class PaysFormTest extends PayFormTest {

	public function testEmpty(): void {
		$model = $this->createForm();
		$this->tester->assertFalse($model->validate());
		$this->tester->assertEmpty($model->generatePays());
		$this->tester->assertSame('Value with VAT cannot be blank.', $model->getFirstError('value'));
		$this->tester->assertSame('Payment at is required when Deadline at is empty.', $model->getFirstError('payment_at'));
		$this->tester->assertSame('First deadline at is required when Payment at is empty.', $model->getFirstError('deadline_at'));
	}

	public function testGenerateOne(): void {
		$model = $this->createForm();
		$model->count = 1;
		$model->value = 123;
		$model->vat = 23;
		$model->deadline_at = '2020-01-01';
		$pays = $model->generatePays();
		$this->tester->assertCount(1, $pays);
		$pay = reset($pays);
		$this->tester->assertInstanceOf(PayInterface::class, $pay);
		$this->tester->assertTrue($pay->getValue()->equals(new Decimal(123)));
		$this->tester->assertNotNull($pay->getDeadlineAt());
		$this->tester->assertSame('2020-01-01', $pay->getDeadlineAt()->format($model->dateFormat));
		$this->tester->assertNull($pay->getPaymentAt());
	}

	public function testGenerateDouble(): void {
		$model = $this->createForm();
		$model->count = 2;
		$model->value = 246;
		$model->vat = 23;
		$model->deadline_at = '2020-01-31';
		$model->deadlineRange = PaysForm::DEADLINE_LAST_DAY_OF_MONTH;
		$pays = $model->generatePays(false);
		$this->tester->assertCount(2, $pays);
		$pay1 = $pays[0];
		$this->tester->assertTrue($pay1->getValue()->equals(new Decimal(123)));
		$this->tester->assertNull($pay1->getPaymentAt());
		$this->tester->assertNotNull($pay1->getDeadlineAt());
		$this->tester->assertSame('2020-01-31', $pay1->getDeadlineAt()->format($model->dateFormat));

		$pay2 = $pays[1];
		$this->tester->assertInstanceOf(PayInterface::class, $pay2);
		$this->tester->assertTrue($pay2->getValue()->equals(new Decimal(123)));
		$this->tester->assertNull($pay2->getPaymentAt());
		$this->tester->assertNotNull($pay2->getDeadlineAt());
		$this->tester->assertSame('2020-02-29', $pay2->getDeadlineAt()->format($model->dateFormat));
	}

	protected function createForm(): PaysForm {
		return new PaysForm();
	}
}

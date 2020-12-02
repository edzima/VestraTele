<?php

namespace common\tests\unit\settlement;

use common\models\settlement\PayForm;
use common\models\settlement\PayInterface;
use common\tests\unit\Unit;
use DateTime;
use Decimal\Decimal;

class PayFormTest extends Unit {

	public function testEmpty(): void {
		$model = $this->createForm();
		$this->assertNull($model->generatePay());
		$this->tester->assertSame('Value with VAT cannot be blank.', $model->getFirstError('value'));
		$this->tester->assertSame('Payment at is required when Deadline at is empty.', $model->getFirstError('paymentAt'));
		$this->tester->assertSame('Deadline at is required when Payment at is empty.', $model->getFirstError('deadlineAt'));
	}

	public function testValueAsString(): void {
		$model = $this->createForm();
		$model->value = 'some-value';
		$this->tester->assertFalse($model->validate());
		$this->tester->assertSame('Value with VAT must be a number.', $model->getFirstError('value'));
	}

	public function testValueAsZero(): void {
		$model = $this->createForm();
		$model->value = 0;
		$this->tester->assertFalse($model->validate());
		$this->tester->assertSame('Value with VAT must be no less than 1.', $model->getFirstError('value'));
	}

	public function testCreateWithPayment(): void {
		$model = $this->createForm();
		$model->value = 123;
		$model->vat = 23;
		$model->payment_at = '2020-01-01';
		$pay = $model->generatePay();
		$this->tester->assertInstanceOf(PayInterface::class, $pay);
		$this->tester->assertTrue($pay->getValue()->equals(new Decimal(123)));
		$this->tester->assertNotNull($pay->getPaymentAt());
		$this->tester->assertSame('2020-01-01', $pay->getPaymentAt()->format($model->dateFormat));
		$this->tester->assertNull($pay->getDeadlineAt());
	}

	public function testCreateWithDeadline(): void {
		$model = $this->createForm();
		$model->value = 123;
		$model->vat = 23;
		$model->deadline_at = '2020-01-01';
		$pay = $model->generatePay();
		$this->tester->assertNotNull($pay);
		$this->tester->assertTrue($pay->getValue()->equals(new Decimal(123)));
		$this->tester->assertNotNull($pay->getDeadlineAt());
		$this->tester->assertSame('2020-01-01', $pay->getDeadlineAt()->format($model->dateFormat));
	}

	public function testCreateWithDeadlineRange(): void {
		$model = $this->createForm();
		$model->value = 123;
		$model->deadlineInterval = PayForm::DEADLINE_INTERVAL_3_DAYS;
		$pay = $model->generatePay();
		$this->tester->assertNotNull($pay);
		$this->tester->assertSame((new DateTime())->modify('3 days')->format($model->dateFormat), $pay->getDeadlineAt()->format($model->dateFormat));
	}

	public function testGetDeadlineFromRange(): void {
		$model = $this->createForm();
		$model->deadlineInterval = PayForm::DEADLINE_INTERVAL_7_DAYS;
		$this->tester->assertNotNull($model->getDeadlineAt());
		$date = new DateTime();
		$this->tester->assertSame(
			$date->modify('+ 7 days')->format($model->dateFormat),
			$model->getDeadlineAt()->format($model->dateFormat)
		);
	}

	protected function createForm(): PayForm {
		return new PayForm();
	}

}

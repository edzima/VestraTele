<?php

namespace common\tests\unit\settlement;

use common\models\settlement\PayForm;
use common\models\settlement\PayInterface;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use DateTime;
use Decimal\Decimal;

class PayFormTest extends Unit {

	use UnitModelTrait;

	protected PayForm $model;

	public function _before() {
		parent::_before();
		$this->model = $this->createForm();
	}

	public function testEmpty(): void {
		$this->thenUnsuccessValidate();
		$this->assertNull($this->generatePay());
		$this->thenSeeError('Value with VAT cannot be blank.', 'value');
		$this->thenSeeError('Payment at is required when Deadline at is empty.', 'payment_at');
		$this->thenSeeError('Deadline at is required when Payment at is empty.', 'deadline_at');
	}

	public function testValueAsString(): void {
		$this->model->value = 'some-value';
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Value with VAT must be a number.', 'value');
	}

	public function testValueAsZero(): void {
		$this->model->value = 0;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Value with VAT must be no less than 1.', 'value');
	}

	public function testCreateWithPayment(): void {
		$model = $this->getModel();
		$model->value = 123;
		$model->vat = 23;
		$model->payment_at = '2020-01-01';
		$this->thenSuccessValidate();
		$pay = $this->generatePay();
		$this->tester->assertInstanceOf(PayInterface::class, $pay);
		$this->tester->assertTrue($pay->getValue()->equals(new Decimal(123)));
		$this->tester->assertNotNull($pay->getPaymentAt());
		$this->tester->assertSame('2020-01-01', $pay->getPaymentAt()->format($model->dateFormat));
		$this->tester->assertNull($pay->getDeadlineAt());
	}

	public function testCreateWithDeadline(): void {
		$model = $this->getModel();
		$model->value = 123;
		$model->vat = 23;
		$model->deadline_at = '2020-01-01';
		$pay = $this->generatePay();
		$this->tester->assertNotNull($pay);
		$this->tester->assertTrue($pay->getValue()->equals(new Decimal(123)));
		$this->tester->assertNotNull($pay->getDeadlineAt());
		$this->tester->assertSame('2020-01-01', $pay->getDeadlineAt()->format($model->dateFormat));
	}

	public function testCreateWithDeadlineRange(): void {
		$model = $this->getModel();
		$model->value = 123;
		$model->deadlineInterval = PayForm::DEADLINE_INTERVAL_3_DAYS;
		$pay = $this->generatePay();
		$this->tester->assertNotNull($pay);
		$this->tester->assertNotNull($pay->getDeadlineAt());
		$this->tester->assertSame((new DateTime())->modify('3 days')->format($model->dateFormat), $pay->getDeadlineAt()->format($model->dateFormat));
	}

	public function testGetDeadlineFromRange(): void {
		$model = $this->getModel();
		$model->deadlineInterval = PayForm::DEADLINE_INTERVAL_7_DAYS;
		$this->tester->assertNotNull($model->getDeadlineAt());
		$date = new DateTime();
		$this->tester->assertSame(
			$date->modify('+ 7 days')->format($model->dateFormat),
			$model->getDeadlineAt()->format($model->dateFormat)
		);
	}

	protected function createForm(array $config = []): PayForm {
		return new PayForm($config);
	}

	public function getModel(): PayForm {
		return $this->model;
	}

	private function generatePay(): ?PayInterface {
		return $this->model->generatePay();
	}
}

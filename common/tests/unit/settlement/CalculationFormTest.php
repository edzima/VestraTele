<?php

namespace common\tests\unit\settlement;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\settlement\CalculationForm;
use common\models\settlement\PayInterface;
use DateTime;
use Decimal\Decimal;
use yii\base\InvalidConfigException;

class CalculationFormTest extends PayFormTest {

	public const AGENT_ID = 300;

	private function haveFixtures(): void {
		$this->tester->haveFixtures(IssueFixtureHelper::fixtures(), IssueFixtureHelper::settlements());
	}

	public function testWithoutOwner(): void {
		$this->tester->expectThrowable(new InvalidConfigException('Owner must be set as integer.'), function () {
			$model = $this->createForm(null);
			$this->assertFalse($model->save());
		});
	}

	public function testEmpty(): void {
		parent::testEmpty();
		$model = $this->createForm();
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Issue Id cannot be blank.', $model->getFirstError('issue_id'));
		$this->tester->assertSame('Type cannot be blank.', $model->getFirstError('type'));
		$this->tester->assertSame('Provider cannot be blank.', $model->getFirstError('providerType'));
		$this->tester->assertSame('Owner is invalid.', $model->getFirstError('owner'));
	}

	public function testCreateWithDeadline(): void {
		$this->haveFixtures();
		$model = $this->createForm();
		$model->issue_id = 1;
		$model->type = IssuePayCalculation::TYPE_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->value = 123;
		$model->vat = 23;
		$model->deadline_at = '2020-01-01';
		$pay = $model->generatePay();
		$this->tester->assertNotNull($pay);
		$this->tester->assertNotNull($pay->getDeadlineAt());
		$this->tester->assertSame('2020-01-01', $pay->getDeadlineAt()->format($model->dateFormat));
	}

	public function testCreateWithDeadlineRange(): void {
		$this->haveFixtures();
		$model = $this->createForm();
		$model->issue_id = 1;
		$model->type = IssuePayCalculation::TYPE_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->value = 123;
		$model->deadlineInterval = CalculationForm::DEADLINE_INTERVAL_3_DAYS;
		$pay = $model->generatePay();
		$this->tester->assertNotNull($pay);
		$this->tester->assertSame((new DateTime())->modify('3 days')->format($model->dateFormat), $pay->getDeadlineAt()->format($model->dateFormat));
	}


	public function testInvalidType(): void {
		$model = $this->createForm();
		$model->type = 'invalid-type';
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Type must be an integer.', $model->getFirstError('type'));
		$model->type = 1212412412412412;
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Type is invalid.', $model->getFirstError('type'));
	}

	public function testInvalidProviderType(): void {
		$model = $this->createForm();
		$model->providerType = -1;
		$this->tester->assertFalse($model->validate(['providerType']));
		$this->tester->assertSame('Provider is invalid.', $model->getFirstError('providerType'));
		$this->tester->expectThrowable(
			new InvalidConfigException('Invalid provider type.'),
			function () use ($model) {
				$model->getProviderId();
			});
	}

	public function testCreate(): void {
		$this->haveFixtures();
		$model = $this->createForm();
		$model->issue_id = 1;
		$model->value = 12300;
		$model->vat = 23;
		$model->type = IssuePayCalculation::TYPE_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(IssuePayCalculation::class, [
			'owner_id' => static::AGENT_ID,
			'issue_id' => 1,
			'value' => 12300,
			'type' => IssuePayCalculation::TYPE_HONORARIUM,
			'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
			'payment_at' => '2020-02-02',
			'provider_id' => $model->getModel()->issue->customer->id,
		]);

		$this->tester->seeRecord(IssuePay::class, [
			'calculation_id' => $model->getModel()->id,
			'value' => 12300,
			'vat' => 23,
			'pay_at' => '2020-02-02',
		]);
		$this->tester->assertTrue($model->getModel()->isPayed());
		IssuePayCalculation::deleteAll();
	}

	public function testCreateWithPayment(): void {
		$this->haveFixtures();
		$model = $this->createForm();
		$model->issue_id = 1;
		$model->value = 123;
		$model->vat = 23;
		$model->type = IssuePayCalculation::TYPE_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-01-01';
		$pay = $model->generatePay();
		$this->tester->assertInstanceOf(PayInterface::class, $pay);
		$this->tester->assertTrue($pay->getValue()->equals(new Decimal(123)));
		$this->tester->assertNotNull($pay->getPaymentAt());
		$this->tester->assertSame('2020-01-01', $pay->getPaymentAt()->format($model->dateFormat));
		$this->tester->assertNull($pay->getDeadlineAt());
	}


	protected function createForm(?int $ownerId = self::AGENT_ID): CalculationForm {
		$model = new CalculationForm();
		if ($ownerId !== null) {
			$model->setOwner($ownerId);
		}
		return $model;
	}

}

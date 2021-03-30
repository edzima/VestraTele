<?php

namespace common\tests\unit\settlement;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\settlement\CalculationForm;
use common\models\settlement\PayInterface;
use DateTime;
use Decimal\Decimal;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class CalculationFormTest extends PayFormTest {

	private IssueFixtureHelper $issueFixture;

	private const DEFAULT_OWNER_ID = SettlementFixtureHelper::OWNER_JOHN;

	public function _before() {
		parent::_before();
		$this->issueFixture = new IssueFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::entityResponsible(),
			IssueFixtureHelper::users(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::cost(true),
			SettlementFixtureHelper::pay(),
			SettlementFixtureHelper::owner(),
		);
	}

	public function testEmpty(): void {
		parent::testEmpty();
		$model = $this->createForm();
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Type cannot be blank.', $model->getFirstError('type'));
		$this->tester->assertSame('Provider cannot be blank.', $model->getFirstError('providerType'));
	}

	public function testCreateWithDeadline(): void {
		$model = $this->createForm();
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
		$model = $this->createForm();
		$model->type = IssuePayCalculation::TYPE_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->value = 123;
		$model->deadlineInterval = CalculationForm::DEADLINE_INTERVAL_3_DAYS;
		$pay = $model->generatePay();
		$this->tester->assertNotNull($pay);
		$this->tester->assertSame((new DateTime())->modify('3 days')->format($model->dateFormat), $pay->getDeadlineAt()->format($model->dateFormat));
	}

	public function testInvalidCost(): void {
		$model = $this->createForm();
		$model->costs_ids = [12121212];
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Costs is invalid.', $model->getFirstError('costs_ids'));
	}

	public function testCostAsEmptyString(): void {
		$model = $this->createForm();
		$model->costs_ids = '';
		$model->value = 12300;
		$model->vat = 23;
		$model->type = IssuePayCalculation::TYPE_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		$this->tester->assertTrue($model->save());
	}

	public function testWithCostAsArray(): void {
		$model = $this->createForm();
		$model->costs_ids = [1];
		$model->value = 12300;
		$model->vat = 23;
		$model->type = IssuePayCalculation::TYPE_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		$this->tester->assertTrue($model->save());
		$costs = $model->getModel()->costs;
		$this->tester->assertCount(1, $costs);
	}

	public function testWithCostAsString(): void {
		$model = $this->createForm();
		$model->costs_ids = 1;
		$model->value = 12300;
		$model->vat = 23;
		$model->type = IssuePayCalculation::TYPE_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		$this->tester->assertTrue($model->save());
		$costs = $model->getModel()->costs;
		$this->tester->assertCount(1, $costs);
	}

	public function testWithCosts(): void {
		$model = $this->createForm();
		$model->costs_ids = [1, 2];
		$model->value = 12300;
		$model->vat = 23;
		$model->type = IssuePayCalculation::TYPE_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		$this->tester->assertTrue($model->save());
		$costs = $model->getModel()->costs;
		$this->tester->assertCount(2, $costs);
	}

	public function testUnlinkCost(): void {
		$model = $this->createForm();
		$model->costs_ids = [1, 2];
		$model->value = 12300;
		$model->vat = 23;
		$model->type = IssuePayCalculation::TYPE_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		$this->tester->assertTrue($model->save());
		$newForm = CalculationForm::createFromModel($model->getModel());
		$newForm->costs_ids = [1];
		$newForm->validate();
		$this->tester->assertTrue($newForm->save());
		$costs = $newForm->getModel()->costs;
		$this->tester->assertCount(1, $costs);
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
		$model = $this->createForm();
		$model->value = 12300;
		$model->vat = 23;
		$model->type = IssuePayCalculation::TYPE_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(IssuePayCalculation::class, [
			'owner_id' => static::DEFAULT_OWNER_ID,
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
	}

	public function testCreateWithPayment(): void {
		$model = $this->createForm();
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

	protected function createForm(array $config = []): CalculationForm {
		$issue = ArrayHelper::remove($config, 'issue', $this->issueFixture->grabIssue(0));
		$ownerId = ArrayHelper::remove($config, 'ownerId', static::DEFAULT_OWNER_ID);
		return new CalculationForm($ownerId, $issue, $config);
	}

}

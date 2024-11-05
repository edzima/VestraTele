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
		$this->issueFixture = new IssueFixtureHelper($this->tester);
		parent::_before();
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
			SettlementFixtureHelper::type(),
		);
	}

	public function testEmpty(): void {
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Value with VAT cannot be blank.', 'value');
		$this->thenDontSeeError('payment_at');
		$this->thenDontSeeError('deadline_at');
		$this->thenSeeError('Type cannot be blank.', 'type_id');
		$this->thenSeeError('Provider cannot be blank.', 'providerType');
	}

	public function testCreateWithDeadline(): void {
		$model = $this->getModel();
		$model->type_id = SettlementFixtureHelper::TYPE_ID_HONORARIUM;
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
		$model->type_id = SettlementFixtureHelper::TYPE_ID_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->value = 123;
		$model->deadlineInterval = CalculationForm::DEADLINE_INTERVAL_3_DAYS;
		$pay = $model->generatePay();
		$this->tester->assertNotNull($pay);
		$this->tester->assertSame((new DateTime())->modify('3 days')->format($model->dateFormat), $pay->getDeadlineAt()->format($model->dateFormat));
	}

	public function testInvalidCost(): void {
		$this->giveForm([
			'costs_ids' => [11212121212],
		]);
		$this->thenUnsuccessSave();
		$this->thenSeeError('Costs is invalid.', 'costs_ids');
	}

	public function testCostAsEmptyString(): void {
		$this->giveForm();
		$model = $this->getModel();
		$model->costs_ids = '';
		$model->value = 12300;
		$model->vat = 23;
		$model->type_id = SettlementFixtureHelper::TYPE_ID_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		codecept_debug($model->getTypesNames());
		$this->thenSuccessSave();
	}

	public function testWithCostAsArray(): void {
		$this->giveForm();
		$model = $this->getModel();
		$model->costs_ids = [1];
		$model->value = 12300;
		$model->vat = 23;
		$model->type_id = SettlementFixtureHelper::TYPE_ID_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		$this->thenSuccessSave();
		$costs = $model->getModel()->costs;
		$this->tester->assertCount(1, $costs);
	}

	public function testWithCostAsString(): void {
		$this->giveForm();
		$model = $this->getModel();
		$model->costs_ids = 1;
		$model->value = 12300;
		$model->vat = 23;
		$model->type_id = SettlementFixtureHelper::TYPE_ID_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		$this->thenSuccessSave();
		$costs = $model->getModel()->costs;
		$this->tester->assertCount(1, $costs);
	}

	public function testWithCosts(): void {
		$this->giveForm();
		$model = $this->getModel();
		$model->costs_ids = [1, 2];
		$model->value = 12300;
		$model->vat = 23;
		$model->type_id = SettlementFixtureHelper::TYPE_ID_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		$this->thenSuccessSave();
		$costs = $model->getModel()->costs;
		$this->tester->assertCount(2, $costs);
	}

	public function testUnlinkCost(): void {
		$this->giveForm();
		$model = $this->getModel();
		$model->costs_ids = [1, 2];
		$model->value = 12300;
		$model->vat = 23;
		$model->type_id = SettlementFixtureHelper::TYPE_ID_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		$this->thenSuccessSave();

		$this->model = CalculationForm::createFromModel($model->getModel());
		$model = $this->getModel();

		$model->costs_ids = [1];
		$this->thenSuccessSave();
		$costs = $model->getModel()->costs;
		$this->tester->assertCount(1, $costs);
	}

	public function testInvalidType(): void {
		$model = $this->createForm();
		$model->type_id = 1212412412412412;
		$this->thenUnsuccessSave();
		$this->thenSeeError('Type cannot be blank.', 'type_id');
	}

	public function testInvalidProviderType(): void {
		$model = $this->createForm();
		$model->providerType = -1;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Provider cannot be blank.', 'providerType');
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
		$model->type_id = SettlementFixtureHelper::TYPE_ID_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-02-02';
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(IssuePayCalculation::class, [
			'owner_id' => static::DEFAULT_OWNER_ID,
			'issue_id' => 1,
			'value' => 12300,
			'type_id' => SettlementFixtureHelper::TYPE_ID_HONORARIUM,
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
		$model->type_id = SettlementFixtureHelper::TYPE_ID_HONORARIUM;
		$model->providerType = IssuePayCalculation::PROVIDER_CLIENT;
		$model->payment_at = '2020-01-01';
		$pay = $model->generatePay();
		$this->tester->assertInstanceOf(PayInterface::class, $pay);
		$this->tester->assertTrue($pay->getValue()->equals(new Decimal(123)));
		$this->tester->assertNotNull($pay->getPaymentAt());
		$this->tester->assertSame('2020-01-01', $pay->getPaymentAt()->format($model->dateFormat));
		$this->tester->assertNull($pay->getDeadlineAt());
	}

	public function testCreateWithoutPayementAndDeadlineAt(): void {
		$this->giveForm([
			'value' => 123,
			'type_id' => SettlementFixtureHelper::TYPE_ID_HONORARIUM,
			'providerType' => IssuePayCalculation::PROVIDER_CLIENT,
		]);
		$this->thenSuccessSave();
		$this->tester->seeRecord(IssuePayCalculation::class, [
			'value' => 123,
			'type_id' => SettlementFixtureHelper::TYPE_ID_HONORARIUM,
			'payment_at' => null,
		]);
		$this->tester->seeRecord(IssuePay::class, [
			'calculation_id' => $this->getModel()->getModel()->getId(),
			'value' => 123,
			'pay_at' => null,
			'deadline_at' => null,
		]);
	}

	protected function createForm(array $config = []): CalculationForm {
		$issue = ArrayHelper::remove($config, 'issue', $this->issueFixture->grabIssue(0));
		$ownerId = ArrayHelper::remove($config, 'ownerId', static::DEFAULT_OWNER_ID);
		return new CalculationForm($ownerId, $issue, $config);
	}

	protected function giveForm(array $config = []): void {
		$this->model = $this->createForm($config);
	}

	public function getModel(): CalculationForm {
		/**
		 * @var CalculationForm $model
		 */
		$model = parent::getModel();
		return $model;
	}

}

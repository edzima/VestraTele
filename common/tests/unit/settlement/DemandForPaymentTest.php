<?php

namespace common\tests\unit\settlement;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssuePay;
use common\models\issue\IssueUser;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use console\models\DemandForPayment;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;

class DemandForPaymentTest extends Unit {

	use UnitModelTrait;

	private const DEFAULT_VALUE = 100;
	private SettlementFixtureHelper $settlementFixture;
	private DemandForPayment $model;

	public function _before() {
		parent::_before();
		$this->settlementFixture = new SettlementFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(true),
			IssueFixtureHelper::types(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::owner(),
			SettlementFixtureHelper::pay(),
			MessageTemplateFixtureHelper::fixture(MessageTemplateFixtureHelper::DIR_ISSUE_PAY_DEMAND)
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Which cannot be blank.', 'which');
		$this->tester->assertNull($this->model->delayedDays);
	}

	public function testInvalidWhich(): void {
		$this->giveModel();
		$this->model->which = 'twenty';
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Which is invalid.', 'which');
	}

	public function testAlreadyPayment(): void {
		$this->giveModel();

		$this->tester->expectThrowable(new InvalidArgumentException('Pay cannot be payed.'), function () {
			$this->model->setPay($this->getPay(null, ['pay_at' => '2020-02-02']));
		});
	}

	public function testSamePayStatusAsDemand(): void {
		$this->giveModel();
		$this->model->which = DemandForPayment::WHICH_FIRST;
		$this->tester->expectThrowable(new InvalidArgumentException('Pay cannot have same demand status.'), function () {
			$pay = $this->getPay(
				date('Y-m-d', strtotime('yesterday')), [
					'status' => IssuePay::STATUS_DEMAND_FOR_PAYMENT_FIRST,
				]
			);
			$this->model->setPay($pay);
		});
	}

	public function testNotPaymentWithoutDeadline(): void {
		$this->giveModel();

		$this->tester->expectThrowable(new InvalidArgumentException('Pay must have deadline.'), function () {
			$this->model->setPay($this->getPay(null));
		});
	}

	public function testNotPaymentWithDeadlineNotDelayed(): void {
		$this->giveModel();
		$this->tester->expectThrowable(new InvalidArgumentException('Pay must be delayed.'), function () {
			$pay = $this->getPay(
				date('Y-m-d', strtotime('tomorrow')),
			);
			$this->model->setPay($pay);
		});
	}

	public function testSingleMark(): void {

		$this->giveModel([
			'which' => DemandForPayment::WHICH_FIRST,
			'smsOwnerId' => UserFixtureHelper::AGENT_PETER_NOWAK,
		]);
		$pay = $this->getPay(
			date('Y-m-d', strtotime('yesterday')),
		);
		$this->model->setPay($pay);

		$this->tester->assertTrue($this->model->markOne());
		$this->tester->assertSame(IssuePay::STATUS_DEMAND_FOR_PAYMENT_FIRST, $pay->getStatus());
		$this->tester->seeJobIsPushed();
		$this->tester->seeEmailIsSent();
	}

	public function testMarkMultipleWithDelayedDays(): void {
		$this->giveModel([
			'which' => DemandForPayment::WHICH_FIRST,
			'smsOwnerId' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'delayedDays' => 1,
		]);
		$this->getPay(
			date('Y-m-d', strtotime('yesterday')),
			['status' => null]
		);
		$this->getPay(
			date('Y-m-d', strtotime('yesterday')),
		);
		$this->getPay(
			date('Y-m-d', strtotime('yesterday')),
			['status' => $this->model->getPayStatus()]
		);
		$this->getPay(
			date('Y-m-d', strtotime('-2 days')),
		);

		$this->tester->assertSame(2, $this->model->markMultiple());
	}

	public function testMarkMultipleWithoutDelayedDays(): void {
		IssuePay::deleteAll();
		$this->giveModel([
			'which' => DemandForPayment::WHICH_FIRST,
			'smsOwnerId' => UserFixtureHelper::AGENT_PETER_NOWAK,
		]);
		$this->getPay(
			date('Y-m-d', strtotime('yesterday')),
			['status' => null]
		);
		$this->getPay(
			date('Y-m-d', strtotime('yesterday')),
		);
		$this->getPay(
			date('Y-m-d', strtotime('yesterday')),
			['status' => $this->model->getPayStatus()]
		);
		$this->getPay(
			date('Y-m-d', strtotime('-2 days')),
		);

		$this->tester->assertSame(3, $this->model->markMultiple());
	}

	public function testDefaultMessageForm(): void {
		$this->giveModel();
		$message = $this->model->createMessage();
		$message->setPay($this->getPay());
		$this->tester->assertTrue($message->sendSmsToAgent);
		$this->tester->assertTrue($message->sendSmsToCustomer);
		$this->tester->assertCount(1, $message->workersTypes);
		$this->tester->assertContains(IssueUser::TYPE_AGENT, $message->workersTypes);
	}

	public function getModel(): DemandForPayment {
		return $this->model;
	}

	private function getPay(string $deadlineAt = null, array $config = []): IssuePay {
		$config['deadline_at'] = $deadlineAt;
		$value = ArrayHelper::remove($config, 'value', static::DEFAULT_VALUE);
		return $this->settlementFixture->findPay(
			$this->settlementFixture->havePay($value, $config)
		);
	}

	private function giveModel(array $config = []) {
		$this->model = new DemandForPayment($config);
	}
}

<?php

namespace common\tests\unit\settlement;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssuePayInterface;
use common\models\settlement\PayPayedForm;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\swiftmailer\Message;

class PayPayedFormTest extends Unit {

	use UnitModelTrait;

	private SettlementFixtureHelper $settlementFixture;
	private PayPayedForm $model;
	private bool $pay;

	public function _before() {
		parent::_before();
		$this->settlementFixture = new SettlementFixtureHelper($this->tester);
	}

	/**
	 * @return array
	 */
	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(),
			SettlementFixtureHelper::pay(),
			SettlementFixtureHelper::settlement(),
		);
	}

	public function testPayedPay(): void {
		$this->tester->expectThrowable(new InvalidConfigException('$pay can not be payed.'), function () {
			$this->giveModel($this->grabPay('payed'));
		});
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->whenPay(null);
		$this->thenUnsuccessPay();
		$this->thenSeeError('Pay at cannot be blank.', 'date');
	}

	public function testInvalidDate(): void {
		$this->giveModel();
		$this->whenPay('invalid-date');
		$this->thenUnsuccessPay();
	}

	public function testPay(): void {
		$this->giveModel();
		$this->whenPay('2021-01-01');
		$this->settlementFixture->seePay([
			'calculation_id' => $this->model->getPay()->getSettlementId(),
			'value' => $this->model->getPay()->getValue()->toFixed(2),
			'pay_at' => '2021-01-01',
		]);
	}

	public function testEmailsForNotPayed(): void {
		$this->giveModel();
		$model = $this->model;
		$this->tester->assertFalse($model->sendEmailToCustomer());
		$this->tester->dontSeeEmailIsSent();
		$this->tester->assertEmpty($model->sendEmailsToWorkers());
		$this->tester->dontSeeEmailIsSent();
	}

	public function testEmailToCustomer(): void {
		$this->giveModel();
		$this->whenPay('2020-01-01');
		$model = $this->model;
		$this->tester->assertTrue($this->model->sendEmailToCustomer());
		$this->tester->seeEmailIsSent();
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertTrue(array_key_exists($model->getPay()->calculation->getIssueModel()->customer->email, $email->getTo()));
		$this->tester->assertStringNotContainsString(
			$this->getSettlementLink(),
			$email->toString(),
		);
	}

	public function testEmailToWorkers(): void {
		$this->giveModel();
		$this->whenPay('2020-01-01');
		$model = $this->model;
		$this->tester->assertTrue($this->model->sendEmailsToWorkers());
		$this->tester->seeEmailIsSent();
		$email = $this->tester->grabLastSentEmail();
		/**
		 * @var Message $email
		 */
		$this->tester->assertTrue(array_key_exists($model->getPay()->calculation->getIssueModel()->agent->email, $email->getTo()));
		$this->tester->assertTrue(array_key_exists($model->getPay()->calculation->getIssueModel()->tele->email, $email->getTo()));

		$this->assertStringContainsString(
			$this->getSettlementLink(),
			$email->toString()
		);
	}

	public function testWithStatus(): void {
		$this->giveModel($this->grabPay('status-analyse'));
		$model = $this->model;
		$this->tester->assertNotNull($model->getPay()->status);

		$this->whenPay('2020-02-02');
		$this->thenSuccessPay();
		$this->tester->assertNull($model->getPay()->status);
	}

	private function giveModel(IssuePayInterface $pay = null): void {
		if ($pay === null) {
			$pay = $this->grabPay('not-payed');
		}
		$this->model = new PayPayedForm($pay);
	}

	protected function grabPay($index): IssuePayInterface {
		return $this->settlementFixture->grabPay($index);
	}

	private function whenPay($date): void {
		$this->model->date = $date;
		$this->pay = $this->model->pay();
	}

	private function thenSuccessPay(): void {
		$this->tester->assertTrue($this->pay);
	}

	private function thenUnsuccessPay(): void {
		$this->tester->assertFalse($this->pay);
	}

	public function getModel(): Model {
		return $this->model;
	}

	private function getSettlementLink(): string {
		return Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['settlement/view']);
	}
}

<?php

namespace common\tests\unit\settlement;

use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssuePayInterface;
use common\models\settlement\PayPayedForm;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

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
			IssueFixtureHelper::types(),
			IssueFixtureHelper::users(),
			SettlementFixtureHelper::pay(),
			SettlementFixtureHelper::settlement(),
			MessageTemplateFixtureHelper::fixture(MessageTemplateFixtureHelper::DIR_ISSUE_PAY_PAYED),
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
		$this->tester->assertTrue($this->model->pushMessages(UserFixtureHelper::AGENT_EMILY_PAT));
	}

	public function testMessagesForNotPayed(): void {
		$this->giveModel();
		$model = $this->model;
		$this->tester->assertFalse($model->pushMessages(UserFixtureHelper::AGENT_EMILY_PAT));
		$this->tester->dontSeeEmailIsSent();
		$this->tester->dontSeeJobIsPushed();
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

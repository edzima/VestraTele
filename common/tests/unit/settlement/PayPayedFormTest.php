<?php

namespace common\tests\unit\settlement;

use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssuePay;
use common\models\settlement\PayPayedForm;
use common\tests\unit\Unit;
use yii\base\InvalidConfigException;

class PayPayedFormTest extends Unit {

	private SettlementFixtureHelper $settlementFixture;

	public function _before() {
		parent::_before();
		$this->settlementFixture = new SettlementFixtureHelper($this->tester);
	}

	/**
	 * @return array
	 */
	public function _fixtures(): array {
		return array_merge(
			SettlementFixtureHelper::pay()
		);
	}

	public function testPayedPay(): void {
		$this->tester->expectThrowable(new InvalidConfigException('$pay can not be payed.'), function () {
			new PayPayedForm(
				$this->grabPay('payed')
			);
		});
	}

	public function testEmpty(): void {
		$model = new PayPayedForm(
			$this->grabPay('not-payed')
		);
		$this->tester->assertFalse($model->pay());
		$this->tester->assertSame('Date cannot be blank.', $model->getFirstError('date'));
	}

	public function testInvalidDate(): void {
		$model = new PayPayedForm(
			$this->grabPay('not-payed')
		);
		$model->date = 'invalid-date';
		$this->tester->assertFalse($model->pay());
	}

	public function testWithoutDate(): void {
		$model = new PayPayedForm(
			$this->grabPay('not-payed')
		);
		$this->tester->assertFalse($model->pay());
	}

	public function testWithDate(): void {
		$model = new PayPayedForm(
			$this->grabPay('not-payed')
		);
		$model->date = '2021-01-01';
		$this->tester->assertTrue($model->pay());
		$this->settlementFixture->seePay([
			'calculation_id' => 1,
			'value' => 1230,
			'vat' => 23,
			'deadline_at' => '2019-01-01',
			'pay_at' => '2021-01-01',
		]);
	}

	public function testWithStatus(): void {
		$model = new PayPayedForm(
			$this->grabPay('status-analyse')
		);
		$this->tester->assertNotNull($model->getPay()->status);
		$model->date = '2020-02-02';

		$this->tester->assertTrue($model->pay());
		$this->tester->assertNull($model->getPay()->status);
	}

	protected function grabPay($index): IssuePay {
		return $this->settlementFixture->grabPay($index);
	}

}

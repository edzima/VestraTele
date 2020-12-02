<?php

namespace common\tests\unit\settlement;

use common\components\PayComponent;
use common\models\settlement\PayInterface;
use common\tests\helpers\PayHelper;
use common\tests\unit\Unit;
use DateTime;
use Decimal\Decimal;

class PayComponentTest extends Unit {

	private PayComponent $component;

	public function _before() {
		parent::_before();
		$this->component = new PayComponent();
	}

	/**
	 * @dataProvider sumProvider
	 * @param Decimal $sum
	 * @param PayInterface[] $pays
	 */
	public function testSum(Decimal $sum, array $pays): void {
		$this->tester->assertTrue($sum
			->equals($this->component->sum($pays))
		);
	}

	/**
	 * @dataProvider payedSumProvider
	 * @param Decimal $sum
	 * @param PayInterface[] $pays
	 */
	public function testPayedSum(Decimal $sum, array $pays): void {
		$this->tester->assertTrue($sum
			->equals($this->component->payedSum($pays))
		);
	}

	public function payedSumProvider(): array {
		return [
			'all-payment' => [
				new Decimal(200), [
					PayHelper::create(100, [
						'paymentAt' => 	new DateTime('2020-02-01'),
					]),
					PayHelper::create(50, [
						'paymentAt' => 	new DateTime('2020-02-01'),
					]),
					PayHelper::create(50, [
						'paymentAt' => 	new DateTime('2020-02-01'),
					]),
				],
			],
			'not-all-payment' => [
				new Decimal(150), [
					PayHelper::create(100, [
						'paymentAt' => 	new DateTime('2020-02-01'),
					]),
					PayHelper::create(50),
					PayHelper::create(50, [
						'paymentAt' => 	new DateTime('2020-02-01'),
					]),
				],
			],
			'without-payment' => [
				new Decimal(0), [
					PayHelper::create(100),
					PayHelper::create(50),
					PayHelper::create(50),
				],
			],
			'empty-pays' => [
				new Decimal(0),
				[],
			],
		];
	}

	public function sumProvider(): array {
		return [
			'all-payment' => [
				new Decimal(200), [
					PayHelper::create(100, [
						'paymentAt' => new DateTime('2020-02-01'),
					]),
					PayHelper::create(50, [
						'paymentAt' =>  new DateTime('2020-02-01'),
					]),
					PayHelper::create(50, [
						'paymentAt' =>  new DateTime('2020-02-01'),
					]),
				],
			],
			'not-all-payment' => [
				new Decimal(200), [
					PayHelper::create(100, [
						'paymentAt' =>  new DateTime('2020-02-01'),
					]),
					PayHelper::create(50),
					PayHelper::create(50, [
						'paymentAt' =>  new DateTime('2020-03-01'),
					]),
				],
			],
			'without-payment' => [
				new Decimal(200), [
					PayHelper::create(100),
					PayHelper::create(50),
					PayHelper::create(50),
				],
			],
			'empty-pays' => [
				new Decimal(0),
				[],
			],
		];
	}
}

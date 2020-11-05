<?php

namespace common\tests\unit;

use common\components\TaxComponent;
use Decimal\Decimal;
use yii\base\InvalidArgumentException;

class TaxComponentTest extends Unit {

	protected TaxComponent $tax;

	public function _before() {
		parent::_before();
		$this->tax = new TaxComponent();
	}

	public function testZeroTax(): void {
		$this->assertSame(123, $this->tax->netto(new Decimal(123), new Decimal(0))->toInt());
	}

	public function testNegativeTax(): void {
		$this->tester->expectThrowable(InvalidArgumentException::class, function () {
			$this->tax->netto(new Decimal(123), new Decimal(-1))->toInt();
		});
	}

	/**
	 * @dataProvider provider
	 */
	public function testNetto($net, $tax, $brutto): void {
		$calculatedNetto = $this->tax->netto(new Decimal($brutto), new Decimal($tax));
		$net = new Decimal($net);
		$this->tester->assertSame($net->toFixed(2), $calculatedNetto->toFixed(2));
	}

	/**
	 * @dataProvider provider
	 */
	public function testBrutto($net, $tax, $brutto): void {
		$calculatedBrutto = $this->tax->brutto(new Decimal($net), new Decimal($tax));
		$brutto = new Decimal($brutto);
		$this->tester->assertSame($brutto->toFixed(2), $calculatedBrutto->toFixed(2));
	}

	public function provider(): array {
		return [
			[100, 23, 123],
			['100', '23', '123'],
			[105, 5, '110.25'],
			[100, 0, 100],
			['487.81', 23, '600.01'],
		];
	}

}

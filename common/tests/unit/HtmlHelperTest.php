<?php

namespace common\tests\unit;

use common\helpers\Html;

class HtmlHelperTest extends Unit {

	public function testHexToRgb(): void {
		$rgb = Html::hexToRgb('#000000');
		$this->tester->assertSame(0, $rgb['r']);
		$this->tester->assertSame(0, $rgb['g']);
		$this->tester->assertSame(0, $rgb['b']);

		$rgb = Html::hexToRgb('000000');
		$this->tester->assertSame(0, $rgb['r']);
		$this->tester->assertSame(0, $rgb['g']);
		$this->tester->assertSame(0, $rgb['b']);

		$rgb = Html::hexToRgb('#FFFFFF');
		$this->tester->assertSame(255, $rgb['r']);
		$this->tester->assertSame(255, $rgb['g']);
		$this->tester->assertSame(255, $rgb['b']);

		$rgb = Html::hexToRgb('FFFFFF');
		$this->tester->assertSame(255, $rgb['r']);
		$this->tester->assertSame(255, $rgb['g']);
		$this->tester->assertSame(255, $rgb['b']);
	}

	public function testCssRgbValueFromHex(): void {
		$rgbValue = Html::cssRgbValueFromHex('#000000');
		$this->tester->assertSame('rgb(0,0,0)', $rgbValue);
	}
}

<?php

namespace common\tests\unit\credit;

use common\modules\credit\components\exception\WiborArchiveException;
use common\modules\credit\components\WiborArchiveComponent;
use common\tests\unit\Unit;
use yii\caching\DummyCache;

class WiborArchiveTest extends Unit {

	private WiborArchiveComponent $wibor;

	public function _before() {
		$this->wibor = new WiborArchiveComponent([
			'cache' => [
				'class' => DummyCache::class,
			],
		]);
	}

	public function testGetData() {
		$data = $this->wibor->getData();
		$this->tester->assertIsArray($data);
		$this->tester->assertNotEmpty($data);
		foreach ($data as $date => $value) {
			$this->tester->assertIsString($date);
			$this->tester->assertIsFloat($value);
		}
	}

	public function testInvalidFile() {
		$this->wibor->archiveCSVPath = 'https://test-not-exist-link.asd';
		$this->tester->expectThrowable(WiborArchiveException::class, function () {
			$this->wibor->getData(false);
		});
	}
}

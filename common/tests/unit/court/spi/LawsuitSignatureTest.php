<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\components\LawsuitSignature;
use common\tests\unit\Unit;

class LawsuitSignatureTest extends Unit {

	private const DEFAULT_SIGNATURE = 'I ACa 35/12';

	private LawsuitSignature $model;

	public function testGettersForDefaultSignature(): void {
		$this->giveModel();
		$this->tester->assertTrue($this->model->explode());
		$this->tester->assertSame('I', $this->model->getDepartmentName());
		$this->tester->assertSame('ACa', $this->model->getRepertoryName());
		$this->tester->assertSame(35, $this->model->getNumber());
		$this->tester->assertSame(12, $this->model->getYear());
	}

	private function giveModel(string $signature = self::DEFAULT_SIGNATURE) {
		$this->model = new LawsuitSignature($signature);
	}
}

<?php

namespace common\tests\unit\czater;

use common\modules\czater\Czater;
use common\modules\czater\entities\Call;
use common\modules\czater\entities\Consultant;
use common\tests\unit\Unit;

class CzaterTest extends Unit {

	private Czater $czater;

	public function _before(): void {
		$this->czater = new Czater([
			'apiKey' => $_ENV['CZATER_API_KEY'],
		]);
	}

	public function testCalls(): void {
		$calls = $this->czater->calls();
		codecept_debug($calls);
		foreach ($calls as $call) {
			$this->tester->assertInstanceOf(Call::class, $call);
		}
	}

	public function testConsultants(): void {
		$consultants = $this->czater->consultants();
		codecept_debug($consultants);
		foreach ($consultants as $consultant) {
			$this->tester->assertInstanceOf(Consultant::class, $consultant);
		}
	}
}

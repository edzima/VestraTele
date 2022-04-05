<?php

namespace common\tests\unit\czater;

use common\modules\czater\Czater;
use common\modules\czater\entities\Call;
use common\modules\czater\entities\Client;
use common\modules\czater\entities\Consultant;
use common\modules\czater\entities\Conv;
use common\tests\unit\Unit;

class CzaterTest extends Unit {

	private Czater $czater;

	public function _before(): void {
		$this->czater = new Czater([
			'apiKey' => $_ENV['CZATER_API_KEY'],
		]);
	}

	public function testClients(): void {
		$models = $this->czater->getClients();
		foreach ($models as $model) {
			$this->tester->assertInstanceOf(Client::class, $model);
		}
	}

	public function testCalls(): void {
		$calls = $this->czater->getCalls();
		foreach ($calls as $call) {
			$this->tester->assertInstanceOf(Call::class, $call);
		}
	}

	public function testConsultants(): void {
		$consultants = $this->czater->getConsultants();
		foreach ($consultants as $consultant) {
			$this->tester->assertInstanceOf(Consultant::class, $consultant);
		}
	}

	public function testConvs(): void {
		$models = $this->czater->getConvs();
		foreach ($models as $model) {
			$this->tester->assertInstanceOf(Conv::class, $model);
		}
	}
}

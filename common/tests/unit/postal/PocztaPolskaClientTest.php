<?php

namespace common\tests\unit\postal;

use common\components\postal\models\Shipment;
use common\components\postal\models\ShipmentDetails;
use common\components\postal\PocztaPolskaClient;
use common\tests\unit\Unit;
use SoapFault;

class PocztaPolskaClientTest extends Unit {

	public function testWithoutAuthHeader(): void {
		$client = new PocztaPolskaClient(PocztaPolskaClient::getWsdlUrl());
		$this->tester->expectThrowable(new SoapFault(null, 'WSDoAllReceiver: Incoming message does not contain required Security header'), function () use ($client) {
			$client->version();
		});
	}

	public function testVersion(): void {
		$client = new PocztaPolskaClient(PocztaPolskaClient::getWsdlUrl());
		$client->setWsHeader();
		$version = $client->version();
		$this->tester->assertIsString($version);
		$this->tester->assertStringContainsString('tt', $version);
	}

	public function testHello(): void {
		$client = new PocztaPolskaClient(PocztaPolskaClient::getWsdlUrl());
		$client->setWsHeader();
		$response = $client->__soapCall('witaj', [
			'parameters' => [
				'imie' => 'Edzima',
			],
		])->return;
		$this->tester->assertSame('Witaj Edzima!', $response);
	}

	public function testCheckShipment(): void {
		$client = new PocztaPolskaClient(PocztaPolskaClient::getWsdlUrl());
		$client->setWsHeader();
		$shipment = $client->checkShipment('testp0');
		$this->tester->assertInstanceOf(Shipment::class, $shipment);
		$this->tester->assertSame('testp0', $shipment->numer);
		$this->tester->assertSame(0, $shipment->status);
		$this->tester->assertNotNull($shipment->danePrzesylki);

		$details = $shipment->danePrzesylki;
		$this->tester->assertInstanceOf(ShipmentDetails::class, $details);
		$this->tester->assertNotEmpty($details->zdarzenia->zdarzenie);

		$shipment = $client->checkShipment('testp1');
		codecept_debug($shipment);
		$this->tester->assertInstanceOf(Shipment::class, $shipment);
		$this->tester->assertSame(1, $shipment->status);
		$this->tester->assertNotEmpty($shipment->danePrzesylki);

		$shipment = $client->checkShipment('testp-1');
		$this->tester->assertInstanceOf(Shipment::class, $shipment);
		$this->tester->assertSame(-1, $shipment->status);
		$this->tester->assertNull($shipment->danePrzesylki);

		$shipment = $client->checkShipment('testp-2');
		$this->tester->assertInstanceOf(Shipment::class, $shipment);
		$this->tester->assertSame(-2, $shipment->status);
		$this->tester->assertNull($shipment->danePrzesylki);

		$shipment = $client->checkShipment('testp-99');
		$this->tester->assertInstanceOf(Shipment::class, $shipment);
		$this->tester->assertSame(-99, $shipment->status);
		$this->tester->assertNull($shipment->danePrzesylki);
	}
}

<?php

namespace common\components\postal;

use common\components\postal\models\Shipment;
use SoapFault;
use Yii;
use yii\base\Component;

class PocztaPolska extends Component {

	public string $clientType = PocztaPolskaClient::CLIENT_TYPE_INDIVIDUAL;

	public string $username = 'sledzeniepp';
	public string $password = 'PPSA';

	private ?PocztaPolskaClient $client = null;
	private ?Shipment $shipment;

	public function checkShipment(string $number): void {
		$this->shipment = null;
		try {
			$client = $this->getClient();
			$shipment = $client->checkShipment($number);
			if (!$shipment->isOk()) {
				Yii::warning('Check Shipment: #' . $number . ' is not OK. Status: ' . $shipment->getStatusName(), __METHOD__);
			}
			$this->shipment = $shipment;
		} catch (SoapFault $exception) {
			Yii::error($exception->getMessage(), __METHOD__);
		}
	}

	public function getShipment(): ?Shipment {
		return $this->shipment;
	}

	public function getClient(): PocztaPolskaClient {
		if ($this->client === null) {
			$this->client = new PocztaPolskaClient(PocztaPolskaClient::getWsdlUrl($this->clientType));
			$this->client->username = $this->username;
			$this->client->password = $this->password;
			$this->client->setWsHeader();
		}
		return $this->client;
	}

}

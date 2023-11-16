<?php

namespace common\components\postal;

use yii\base\Component;

class PocztaPolska extends Component {

	public string $clientType = PocztaPolskaClient::CLIENT_TYPE_INDIVIDUAL;

	public string $username = 'sledzeniepp';
	public string $password = 'PPSA';

	private ?PocztaPolskaClient $client = null;

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

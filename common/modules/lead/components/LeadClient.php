<?php

namespace common\modules\lead\components;

use frontend\controllers\ApiLeadController;
use Yii;
use yii\base\Component;
use yii\httpclient\Client;

class LeadClient extends Component {

	public string $baseUrl;
	protected array $clientOptions = [];

	private ?Client $client = null;

	/**
	 * @see ApiLeadController::actionCustomer()
	 */
	protected const ROUTE_CUSTOMER = '/lead/api/customer';

	public function init() {
		parent::init();
		$this->clientOptions['baseUrl'] = $this->baseUrl;
	}

	public function addFromCustomer(array $data): ?int {
		$client = $this->getClient();
		$response = $client
			->post(static::ROUTE_CUSTOMER, $data)
			->setData($data)
			->send();
		if ($response->isOk) {
			return true;
		}
		Yii::error([
			'msg' => 'Response is not ok',
			'response' => $response,
		], __METHOD__);
		return false;
	}

	protected function getClient(): Client {
		if ($this->client === null) {
			$this->client = new Client($this->clientOptions);
		}
		return $this->client;
	}

}

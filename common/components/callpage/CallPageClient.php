<?php

namespace common\components\callpage;

use common\helpers\ArrayHelper;
use Yii;
use yii\base\Component;
use yii\httpclient\Client;

class CallPageClient extends Component {

	public string $apiKey;

	public string $baseUrl = 'https://core.callpage.io/api/v1/external';
	protected array $clientOptions = [];

	private ?Client $client = null;

	public function init() {
		parent::init();
		$this->clientOptions['baseUrl'] = $this->baseUrl;
	}

	public function simpleCall(int $widgetId, string $tel, ?int $department_id = null, ?int $manager_id = null): bool {
		$data = [
			'id' => $widgetId,
			'tel' => $tel,
		];
		if ($department_id !== null) {
			$data['department_id'] = $department_id;
		}
		if ($manager_id !== null) {
			$data['manager_id'] = $manager_id;
		}
		$client = $this->getClient();
		$response = $client
			->post('widgets/call', $data, $this->authorizationHeaders())
			->send();
		$responseData = $response->getData();
		$hasError = ArrayHelper::getValue($responseData, 'hasError', null);
		if ($hasError) {
			Yii::error([
				'response' => $response->getData(),
				'data' => $data,
			], __METHOD__);
			return false;
		}
		return true;
	}

	public function callOrSchedule(int $widgetId, string $tel, ?int $department_id = null): bool {
		$data = [
			'id' => $widgetId,
			'tel' => $tel,
		];
		if ($department_id !== null) {
			$data['department_id'] = $department_id;
		}
		$client = $this->getClient();
		$response = $client
			->post('widgets/call-or-schedule', $data, $this->authorizationHeaders())
			->send();
		$responseData = $response->getData();
		$hasError = ArrayHelper::getValue($responseData, 'hasError', null);
		if ($hasError) {
			Yii::error([
				'response' => $response->getData(),
				'data' => $data,
			], __METHOD__);
			return false;
		}
		return true;
	}

	protected function getClient(): Client {
		if ($this->client === null) {
			$this->client = new Client($this->clientOptions);
		}
		return $this->client;
	}

	private function authorizationHeaders(): array {
		return [
			'Authorization' => $this->apiKey,
		];
	}
}

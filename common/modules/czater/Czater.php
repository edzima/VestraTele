<?php

namespace common\modules\czater;

use common\modules\czater\entities\Call;
use common\modules\czater\entities\Client;
use common\modules\czater\entities\Consultant;
use common\modules\czater\entities\Conv;
use Yii;
use yii\base\Component;
use yii\helpers\Json;

class Czater extends Component {

	private const API_URL = 'https://s4.czater.pl/api';

	private const TYPE_CALL = 'call';
	private const TYPE_CONS = 'cons';
	private const TYPE_CONV = 'conv';
	private const TYPE_CLIENTS = 'clients';

	public string $apiKey;

	public function getCall(int $idDataset): ?Call {
		$url = $this->buildUrl(static::TYPE_CALL, [
			'id' => $idDataset,
		]);
		$response = $this->response($url);
		$key = static::typeResponseKeyMap()[static::TYPE_CALL];
		if ($response
			&& $this->responseIsSuccess($response)
			&& isset($response[$key][0])
		) {
			return new Call($this, $response[$key][0]);
		}
		Yii::warning([
			'message' => 'Invalid Call Response',
			'response' => $response,
			'url' => $url,
		], 'czater.getCall');
		return null;
	}

	/**
	 * @param int $offset
	 * @return Call[]|null
	 */
	public function getCalls(int $offset = 0): ?array {
		$url = $this->buildUrl(static::TYPE_CALL, [
			'offset' => $offset,
		]);
		$response = $this->response($url);
		$key = static::typeResponseKeyMap()[static::TYPE_CALL];
		if ($response
			&& $this->responseIsSuccess($response)
			&& isset($response[$key])
		) {
			$calls = [];
			$callsAttributes = $response[$key];
			foreach ($callsAttributes as $attributes) {
				$calls[] = new Call($this, $attributes);
			}
			return $calls;
		}
		Yii::warning([
			'message' => 'Invalid Calls Response',
			'response' => $response,
			'url' => $url,
		], 'czater.getCalls');

		return null;
	}

	public function getConv(int $id): ?Conv {
		$url = $this->buildUrl(static::TYPE_CONV, [
			'id' => $id,
		]);
		$response = $this->response($url);
		$key = static::typeResponseKeyMap()[static::TYPE_CONV];
		if ($response
			&& $this->responseIsSuccess($response)
			&& isset($response[$key][0])
		) {
			return new Conv($this, $response[$key][0]);
		}
		Yii::warning([
			'message' => 'Invalid Conv Response',
			'response' => $response,
			'url' => $url,
			'id' => $id,
		], 'czater.getConv');

		return null;
	}

	public function getConvs(int $offset = 0): ?array {
		$url = $this->buildUrl(static::TYPE_CONV, [
			'offset' => $offset,
		]);
		$response = $this->response($url);
		$responseKey = static::typeResponseKeyMap()[static::TYPE_CONV];
		if ($response
			&& $this->responseIsSuccess($response)
			&& isset($response[$responseKey])
		) {
			$models = [];
			$rows = $response[$responseKey];
			foreach ($rows as $row) {
				$models[] = new Conv($this, $row);
			}
			return $models;
		}
		Yii::warning([
			'message' => 'Invalid Convs Response',
			'response' => $response,
			'url' => $url,
		], 'czater.getConvs');

		return null;
	}

	public function getClient(int $id): ?Client {
		$url = $this->buildUrl(static::TYPE_CLIENTS, [
			'id' => $id,
		]);
		$response = $this->response($url);
		$key = static::typeResponseKeyMap()[static::TYPE_CLIENTS];
		if ($response
			&& $this->responseIsSuccess($response)
			&& isset($response[$key][0])
		) {
			return new Client($response[$key][0]);
		}
		Yii::warning([
			'message' => 'Invalid Clients Response',
			'response' => $response,
			'url' => $url,
			'id' => $id,
		], 'czater.getClient');

		return null;
	}

	/**
	 * @param int $offset
	 * @return Client[]|null
	 */
	public function getClients(int $offset = 0): ?array {
		$url = $this->buildUrl(static::TYPE_CLIENTS, [
			'offset' => $offset,
		]);
		$response = $this->response($url);
		$responseKey = static::typeResponseKeyMap()[static::TYPE_CLIENTS];
		if ($response
			&& $this->responseIsSuccess($response)
			&& isset($response[$responseKey])
		) {
			$models = [];
			$rows = $response[$responseKey];
			foreach ($rows as $row) {
				$models[] = new Client($row);
			}
			return $models;
		}
		Yii::warning([
			'message' => 'Invalid Clients Response',
			'response' => $response,
			'url' => $url,
		], 'czater.getClients');

		return null;
	}

	/**
	 * @return Consultant[]|null
	 */
	public function getConsultants(): ?array {
		$url = $this->buildUrl(static::TYPE_CONS);
		$response = $this->response($url);
		$consultants = [];
		if ($response
			&& $this->responseIsSuccess($response)
			&& isset($response['cons'])
		) {
			$cons = $response['cons'];
			foreach ($cons as $conAttributes) {
				$consultants[] = new Consultant($conAttributes);
			}
			return $consultants;
		}
		Yii::warning([
			'message' => 'Invalid Consultants Response',
			'response' => $response,
			'url' => $url,
		], 'czater.getConsultants');

		return null;
	}

	protected function response(string $url): ?array {
		Yii::debug('Czater Response for Url: ' . $url, __METHOD__);

		Yii::beginProfile($url, __METHOD__);
		$content = file_get_contents($url);
		Yii::debug($content, __METHOD__);
		Yii::endProfile($content, __METHOD__);

		if ($content) {
			return Json::decode($content);
		}
		return null;
	}

	private function buildUrl(string $type, array $params = []): string {
		$params['type'] = $type;
		$params['apiKey'] = $this->apiKey;
		return static::API_URL . '?' . http_build_query($params);
	}

	private function responseIsSuccess(array $response): bool {
		return isset($response['status']) && $response['status'] === 'success';
	}

	private static function typeResponseKeyMap(): array {
		return [
			static::TYPE_CONS => 'cons',
			static::TYPE_CALL => 'calls',
			static::TYPE_CONV => 'convs',
			static::TYPE_CLIENTS => 'clients',
		];
	}

}

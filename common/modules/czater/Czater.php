<?php

namespace common\modules\czater;

use common\modules\czater\entities\Call;
use common\modules\czater\entities\Consultant;
use Yii;
use yii\base\Component;
use yii\helpers\Json;

class Czater extends Component {

	private const API_URL = 'https://s4.czater.pl/api';

	private const TYPE_CALL = 'call';
	private const TYPE_CONS = 'cons';

	public string $apiKey;

	public function call(int $idDataset): ?Call {
		$url = $this->buildUrl(static::TYPE_CALL, [
			'idDataset' => $idDataset,
		]);
		$response = $this->response($url);
		if ($response
			&& $this->responseIsSuccess($response)
			&& isset($response['calls'])) {
			return new Call($response['calls'][0]);
		}
		Yii::warning([
			'message' => 'Invalid Call Response',
			'response' => $response,
			'url' => $url,
		], 'czater.call');
		return null;
	}

	/**
	 * @param int $offset
	 * @return Call[]|null
	 */
	public function calls(int $offset = 0): ?array {
		$url = $this->buildUrl(static::TYPE_CALL, [
			'offset' => $offset,
		]);
		$response = $this->response($url);
		if ($response
			&& $this->responseIsSuccess($response)
			&& isset($response['calls'])
		) {
			$calls = [];
			$callsAttributes = $response['calls'];
			foreach ($callsAttributes as $attributes) {
				$calls[] = new Call($attributes);
			}
			return $calls;
		}
		Yii::warning([
			'message' => 'Invalid Calls Response',
			'response' => $response,
			'url' => $url,
		], 'czater.calls');

		return null;
	}

	/**
	 * @return Consultant[]|null
	 */
	public function consultants(): ?array {
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
		], 'czater.consultants');

		return null;
	}

	protected function response(string $url): ?array {
		$content = file_get_contents($url);
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
}

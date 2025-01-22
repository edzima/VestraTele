<?php

namespace common\modules\court\modules\spi\components;

use common\modules\court\modules\spi\components\exceptions\SPIApiException;
use common\modules\court\modules\spi\components\exceptions\UnauthorizedSPIApiException;
use common\modules\court\modules\spi\models\AppealInterface;
use common\modules\court\modules\spi\models\court\CourtDepartmentFullDTO;
use common\modules\court\modules\spi\models\court\CourtDepartmentSmallDTO;
use common\modules\court\modules\spi\models\court\RepertoryDTO;
use Yii;
use yii\base\Component;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;
use yii\httpclient\Response;

class SPIApi extends Component
	implements AppealInterface {

	protected const TEST_BASE_URL = 'https://testapi.wroclaw.sa.gov.pl/api/';

	public const EVENT_AFTER_REQUEST = 'afterRequest';
	public const PARAM_PAGE_SIZE = 'size';
	public const PARAM_PAGE = 'page';
	public const PARAM_SORT = 'sort';

	public string $baseUrl = 'https://portal.wroclaw.sa.gov.pl/api';
	public string $username;
	public string $password;

	protected string $appeal = self::DEFAULT_APPEAL;
	public string $appealUrlSchema = 'https://portal.wroclaw.sa.gov.pl/{appeal}/api';

	private const ROUTE_COURT = 'courts';
	private const ROUTE_COURT_DEPARTMENTS = 'court-departments';
	private const ROUTE_DEPARTMENT_REPERTORIES = 'repertories/department';
	private const ROUTE_APPLICATIONS = 'applications';

	protected bool $isTest = false;
	protected const DEFAULT_APPEAL = AppealInterface::APPEAL_WROCLAW;

	private ?Client $client = null;

	private array $clientOptions = [
		'requestConfig' => [
			'format' => Client::FORMAT_JSON,
		],
	];

	private ?string $token = null;

	public static function testApi(): self {
		return new self(static::testApiConfig());
	}

	public static function testApiConfig(): array {
		return [
			'baseUrl' => 'https://testapi.wroclaw.sa.gov.pl/api/',
			'password' => 'Wroclaw123',
			'username' => '83040707012',
			'isTest' => true,
		];
	}

	private const NO_AUTH_ROUTES = [
		self::ROUTE_COURT,
	];

	public function get(string $url, array $params = []): Response {
		$url = $this->buildUrl($url, $params);
		try {
			return $this->getClient()
				->createRequest()
				->setMethod('GET')
				->setUrl($url)
				->send();
		} catch (Exception $exception) {
			throw new SPIApiException($exception->getMessage(), $exception->getCode());
		}
	}

	public function put(string $url): Response {
		try {
			return $this->getClient()
				->createRequest()
				->setMethod('PUT')
				->setUrl($url)
				->send();
		} catch (Exception $exception) {
			throw new SPIApiException($exception->getMessage(), $exception->getCode());
		}
	}

	public function post(string $url, array $data = []): Response {
		try {
			return $this->getClient()
				->createRequest()
				->setMethod('POST')
				->setUrl($url)
				->setData($data)
				->send();
		} catch (Exception $exception) {
			throw new SPIApiException($exception->getMessage(), $exception->getCode());
		}
	}

	protected function setIsTest(bool $isTest): void {
		$this->isTest = $isTest;
	}

	public function getIsTest(): bool {
		return $this->isTest;
	}

	public function setAppeal(string $appeal): self {
		$this->appeal = $appeal;
		$this->baseUrl = $this->getAppealUrl($appeal);
		return $this;
	}

	public function getAppealUrl(string $appeal): string {
		//@todo TEST API only default as Wroclaw Appeal. Not working appeal URLs
		if ($this->isTest) {
			return static::TEST_BASE_URL;
		}
		return str_replace('{appeal}', $appeal, $this->appealUrlSchema);
	}

	public function getCourts(array $params = []) {
		$url = $this->getUrl(static::ROUTE_COURT, $params);
		$response = $this->getClient()
			->createRequest()
			->setUrl($url)
			->setMethod('GET')
			->send();
		codecept_debug($response->getData());
		if ($response->isOk) {
			$totalCount = $this->getTotalCount($response);
			return new ArrayDataProvider([
				'key' => 'id',
				'models' => $response->getData(),
				'totalCount' => $totalCount,
			]);
		}

		Yii::warning($response->getData(), __METHOD__);
		return null;
	}

	public function getAllCourtDepartments(array $pageableParams = []): ?DataProviderInterface {
		$route = static::ROUTE_COURT_DEPARTMENTS;
		$url = $this->getUrl($route, $pageableParams);
		$response = $this->getClient()
			->createRequest()
			->setUrl($url)
			->setMethod('GET')
			->send();
		if ($response->isOk) {
			$totalCount = $this->getTotalCount($response);
			$data = $response->getData();
			$models = [];
			foreach ($data as $datum) {
				$models[] = new CourtDepartmentFullDTO($datum);
			}
			return new ArrayDataProvider([
				'key' => 'id',
				'models' => $models,
				'totalCount' => $totalCount,
				'modelClass' => CourtDepartmentFullDTO::class,
			]);
		}
		Yii::warning($response->getData(), __METHOD__);
		return null;
	}

	public function getCourtDepartments(int $courtId, array $params = []) {
		$route = static::ROUTE_COURT_DEPARTMENTS;
		if ($courtId) {
			$route .= '/court/' . $courtId;
		}
		$url = $this->getUrl($route, $params);
		$response = $this->getClient()
			->createRequest()
			->setUrl($url)
			->setMethod('GET')
			->send();
		if ($response->isOk) {
			$totalCount = $this->getTotalCount($response);
			$data = $response->getData();
			$models = [];
			foreach ($data as $datum) {
				$models[] = new CourtDepartmentSmallDTO($datum);
			}
			return new ArrayDataProvider([
				'key' => 'id',
				'models' => $models,
				'totalCount' => $totalCount,
				'modelClass' => CourtDepartmentSmallDTO::class,
			]);
		}
		Yii::warning($response->getData(), __METHOD__);
		return null;
	}

	public function getDepartmentRepertories(int $departmentId, array $params = []): ?DataProviderInterface {
		$route = static::ROUTE_DEPARTMENT_REPERTORIES . '/' . $departmentId;
		$url = $this->getUrl($route, $params);
		$response = $this->getClient()
			->createRequest()
			->setUrl($url)
			->setMethod('GET')
			->send();
		codecept_debug($response->getData());
		if ($response->isOk) {
			$totalCount = $this->getTotalCount($response);
			$data = $response->getData();
			$models = [];
			foreach ($data as $datum) {
				$models[] = new RepertoryDTO($datum);
			}
			return new ArrayDataProvider([
				'key' => 'id',
				'models' => $models,
				'totalCount' => $totalCount,
				'modelClass' => RepertoryDTO::class,
			]);
		}
		Yii::warning($response->getData(), __METHOD__);
		return null;
	}

	public function authenticate(bool $thrown = true): bool {
		$this->token = null;
		$client = $this->getClient();

		$response = $client->createRequest()
			->setUrl('/authenticate')
			->setData([
				'username' => $this->username,
				'password' => $this->password,
			])
			->setMethod('POST')
			->send();
		if ($response->isOk) {
			$this->token = $response->data['id_token'];
			return true;
		}

		if ($thrown) {
			$this->thrown($response);
		}

		return false;
	}

	protected function getClient(): Client {
		if ($this->client === null) {
			$this->client = new Client($this->clientOptions);
			$this->client->baseUrl = $this->baseUrl;
			$this->client->on(
				Client::EVENT_BEFORE_SEND,
				[$this, 'beforeSend']
			);
			$this->client->on(
				Client::EVENT_AFTER_SEND,
				[$this, 'afterSend'],
			);
		}
		return $this->client;
	}

	protected function getUrl(string $route, array $params = []): string {
		$url = $route;
		if (!empty($params)) {
			$url .= '?' . http_build_query($params);
		}
		return $url;
	}

	protected function beforeSend(RequestEvent $event): void {
		$request = $event->request;
		if (!$this->isAuthRequest($request) && $this->isRequiredAuthUrl($request->getUrl())) {
			$this->authenticate();
			$this->addAuthHeader($request);
		}
	}

	protected function afterSend(RequestEvent $event): void {
		$this->trigger(static::EVENT_AFTER_REQUEST, $event);
		if (!$this->isAuthRequest($event->request)) {
			$this->token = null;
		}
	}

	protected function isAuthRequest(Request $request): bool {
		return strpos($request->getUrl(), '/authenticate') !== false;
	}

	private function isRequiredAuthUrl(string $url): bool {
		if (empty(static::NO_AUTH_ROUTES)) {
			return true;
		}
		$url = str_replace($this->baseUrl, '', $url);
		foreach (static::NO_AUTH_ROUTES as $route) {
			if (str_starts_with($url, $route)) {
				return false;
			}
		}
		return true;
	}

	private function thrown(Response $response) {
		$data = $response->getData();
		$status = $data['status'] ?? $response->getStatusCode();
		switch ($status) {
			case '401':
				throw UnauthorizedSPIApiException::createFromResponseData($data);
		}
		throw SPIApiException::createFromResponseData($data);
	}

	private function addAuthHeader(Request $request): void {
		$request->addHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		]);
	}

	public function getTotalCount(Response $response): int {
		if (isset($response->getHeaders()['X-Total-Count'])) {
			return (int) $response->getHeaders()['X-Total-Count'];
		}
		return count($response->getData());
	}

	private function buildUrl(string $url, array $params = []): string {
		if (!empty($params)) {
			$queryString = http_build_query($params);
			$queryString = str_replace(static::PARAM_SORT . '%5B0%5D=', static::PARAM_SORT . '=', $queryString);
			$url .= '?' . $queryString;
		}
		return $url;
	}

	public function getAppeal(): string {
		return $this->appeal;
	}
}

<?php

namespace common\modules\court\modules\spi\components;

use common\modules\court\modules\spi\components\exceptions\SPIApiException;
use common\modules\court\modules\spi\components\exceptions\UnauthorizedSPIApiException;
use common\modules\court\modules\spi\models\AppealInterface;
use common\modules\court\modules\spi\models\application\ApplicationDTO;
use common\modules\court\modules\spi\models\application\ApplicationType;
use common\modules\court\modules\spi\models\application\ApplicationViewDTO;
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

class SPIApi extends Component implements ApplicationType {

	public const REPOSITORY_LAWSUIT = 'lawsuit';

	public string $baseUrl;
	public string $username;
	public string $password;

	protected string $appeal = self::DEFAULT_APPEAL;
	public string $appealUrlSchema = 'https://portal.wroclaw.sa.gov.pl/{appeal}/api';

	private const ROUTE_COURT = 'courts';
	private const ROUTE_COURT_DEPARTMENTS = 'court-departments';
	private const ROUTE_DEPARTMENT_REPERTORIES = 'repertories/department';
	private const ROUTE_LAWSUITS = 'lawsuits';
	private const ROUTE_APPLICATIONS = 'applications';

	protected const DEFAULT_APPEAL = AppealInterface::APPEAL_WROCLAW;

	private ?Client $client = null;

	private array $clientOptions = [
		'requestConfig' => [
			'format' => Client::FORMAT_JSON,
		],
	];

	private ?string $token = null;

	public static function testApi(): self {
		return new self([
			'baseUrl' => 'https://testapi.wroclaw.sa.gov.pl/api/',
			'password' => 'Wroclaw123',
			'username' => '83040707012',
		]);
	}

	private const NO_AUTH_ROUTES = [
		self::ROUTE_COURT,
	];

	public function get(string $url, array $params = []): Response {
		$url = $this->buildUrl($url, $params);
		try {
			return $this->getClient()
				->createRequest()
				->setUrl($url)
				->send();
		} catch (Exception $exception) {
			throw new SPIApiException($exception->getMessage(), $exception->getCode());
		}
	}

	public function setAppeal(string $appeal): void {
		$this->appeal = $appeal;
		$this->baseUrl = $this->getAppealUrl($appeal);
	}

	public function getAppealUrl(string $appeal): string {
		return str_replace('{appeal}', $appeal, $this->appealUrlSchema);
	}

	public function createApplication(ApplicationDTO $model): bool {
		return true;
		$url = static::ROUTE_APPLICATIONS;
		$respone = $this->getClient()
			->createRequest()
			->setUrl($url)
			->setMethod('POST')
			->setData($model->toArray())
			->send();

		codecept_debug($respone->getData());

		if ($respone->isOk) {
			return true;
		}
		Yii::warning($respone->getData(), __METHOD__);
		return false;
	}

	public function checkApplication(ApplicationDTO &$model): bool {
		$url = static::ROUTE_APPLICATIONS . '/' . 'check';
		$response = $this->getClient()
			->createRequest()
			->setUrl($url)
			->setMethod('POST')
			->setData($model->toArray())
			->send();

		codecept_debug($response->getData());

		if ($response->isOk) {
			$model = ApplicationDTO::createFromResponse($response);
			return true;
		}
		return false;
	}

	public function getApplications(array $params = []): ?DataProviderInterface {
		Yii::warning($params);
		$url = $this->getUrl(static::ROUTE_APPLICATIONS, $params);
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
				$models[] = new ApplicationViewDTO($datum);
			}
			return new ArrayDataProvider([
				'key' => 'id',
				'modelClass' => ApplicationViewDTO::class,
				'models' => $models,
				'totalCount' => $totalCount,
			]);
		}
		Yii::warning($response->getData(), __METHOD__);
		return null;
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
		codecept_debug($response->getData());
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
		if (!$this->isAuthRequest($event->request)) {
			$this->token = null;
		}
		codecept_debug($event->request->getData());
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

	public function getApplicationType(): string {
		return $this->appeal;
	}

	private function buildUrl(string $url, array $params = []) {
		$url = $url;
		if (!empty($params)) {
			$url .= '?' . http_build_query($params);
		}
		return $url;
	}

}

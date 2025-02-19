<?php

namespace common\behaviors;

use Closure;
use common\helpers\Url;
use common\models\query\IdsActiveQuery;
use common\models\SearchModel;
use Yii;
use yii\base\Behavior;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;

class SelectionRouteBehavior extends Behavior {

	public array $actions = ['index'];
	public ?string $selectionParamName = 'selection';
	public string $routeParamName = 'route';
	public bool $rememberUrl = true;
	public string $createUrlParamName = 'ids';

	public string $searchQueryParam = 'searchQuery';

	public ?Closure $createUrl = null;

	/**
	 * @var string|array|SearchModel
	 */
	public $searchModel;

	/**
	 * @var Controller
	 * @inheritdoc
	 */
	public $owner;

	public ?Request $request = null;
	public string $stringSeparator = ',';
	public array $postParams = [
		'ids',
	];

	/**
	 * @inheritdoc
	 */
	public function events(): array {
		return [
			Controller::EVENT_BEFORE_ACTION => 'beforeAction',
		];
	}

	public function init(): void {
		parent::init();
		if ($this->request === null) {
			$this->request = Yii::$app->getRequest();
		}
		if (!empty($this->selectionParamName) && !in_array($this->selectionParamName, $this->postParams)) {
			array_unshift($this->postParams, $this->selectionParamName);
		}
		if (!empty($this->searchQueryParam) && !in_array($this->searchQueryParam, $this->postParams)) {
			array_unshift($this->postParams, $this->searchQueryParam);
		}
	}

	public function beforeAction(): ?Response {
		if ($this->isForAction()) {
			$route = $this->request->post($this->routeParamName);
			if ($route !== null) {
				$ids = $this->getSelectionSearchIds();
				if ($ids !== null) {
					if ($this->rememberUrl) {
						Url::remember();
					}
					$url = $this->createUrl($ids, $route);
					return $this->owner->redirect($url);
				}
			}
//
//			$selection = $this->request->post($this->selectionParamName);
//
//			if ($selection !== null && $route !== null) {
//				if ($this->rememberUrl) {
//					Url::remember();
//				}
//				$url = $this->createUrl($selection, $route);
//				return $this->owner->redirect($url);
//			}
		}
		return null;
	}

	protected function isForAction(): bool {
		return in_array($this->owner->action->id, $this->actions, true);
	}

	protected function createUrl(array $selection, string $baseRoute): string {
		if ($this->createUrl !== null) {
			return call_user_func($this->createUrl, $selection, $baseRoute);
		}
		return Url::to([
			$baseRoute,
			$this->createUrlParamName => $selection,
		]);
	}

	public function getSelectionSearchIds(): ?array {
		foreach ($this->postParams as $param) {
			$ids = $this->getPostValue($param);
			if ($ids !== null) {
				return $ids;
			}
		}
		return null;
	}

	public function getQueryParams(array $params = []): array {
		$params[$this->searchQueryParam] = Json::encode($this->request->queryParams);
		return $params;
	}

	protected function getPostValue(string $param): ?array {
		if ($param == $this->searchQueryParam) {
			return $this->getForSearchQueryParam();
		}

		$ids = $this->request->post($param);
		if (is_array($ids) && !empty($ids)) {
			return $ids;
		}
		if (is_string($ids)) {
			$ids = explode($this->stringSeparator, $ids);
		}
		if ($ids) {
			return $ids;
		}
		return null;
	}

	private function getForSearchQueryParam(): ?array {
		$queryParams = $this->request->post($this->searchQueryParam);
		if ($queryParams) {
			$searchModel = $this->searchModel();

			if ($searchModel) {
				if (!is_array($queryParams)) {
					$queryParams = Json::decode($queryParams);
				}
				$dataProvider = $searchModel->search($queryParams);
				if ($dataProvider instanceof ActiveDataProvider) {
					$query = $dataProvider->query;
					if ($query instanceof IdsActiveQuery) {
						return $query->getIds();
					}
				}
			}
		}
		return null;
	}

	protected function searchModel(): ?SearchModel {
		if ($this->searchModel === null) {
			return null;
		}
		if ($this->searchModel instanceof Closure) {
			return call_user_func($this->searchModel);
		}
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->searchModel);
	}

}

<?php

namespace common\behaviors;

use Closure;
use common\helpers\Url;
use common\models\SearchModel;
use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\Response;

class SelectionRouteBehavior extends Behavior {

	public array $actions = ['index'];
	public string $selectionParamName = 'selection';
	public string $routeParamName = 'route';
	public bool $rememberUrl = true;
	public string $createUrlParamName = 'ids';
	public ?Closure $createUrl = null;

	public SearchModel $searchModel;

	/**
	 * @var Controller
	 * @inheritdoc
	 */
	public $owner;

	/**
	 * @inheritdoc
	 */
	public function events(): array {
		return [
			Controller::EVENT_BEFORE_ACTION => 'beforeAction',
		];
	}

	public function beforeAction(): ?Response {
		if ($this->isForAction()) {
			$route = Yii::$app->request->post($this->routeParamName);
			$selection = Yii::$app->request->post($this->selectionParamName);

			if ($selection !== null && $route !== null) {
				if ($this->rememberUrl) {
					Url::remember();
				}
				$url = $this->createUrl($selection, $route);
				return $this->owner->redirect($url);
			}
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

}

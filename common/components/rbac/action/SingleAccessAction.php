<?php

namespace common\components\rbac\action;

use Closure;
use common\components\rbac\form\SingleActionAccessForm;
use common\helpers\Url;
use InvalidArgumentException;
use Yii;
use yii\base\Action;

class SingleAccessAction extends Action {

	public ?Closure $manager;

	public string $redirectView = 'view';
	public string $redirectViewPrimaryKeyParam = 'id';
	public ?Closure $redirectUrl = null;

	public function run($primaryKey, string $app, string $action) {
		// Ensure the closure is callable
		if (!is_callable($this->manager)) {
			throw new InvalidArgumentException('Closure must be callable.');
		}

		if ($this->redirectUrl === null) {
			$this->redirectUrl = function () use ($primaryKey, $app, $action) {
				return $this->deafultRedirect($primaryKey, $app, $action);
			};
		}

		// Call the closure and pass necessary parameters
		$manager = call_user_func($this->closure, $primaryKey, $app, $action);

		// Create the model with the manager
		$model = new SingleActionAccessForm($manager);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$url = call_user_func($this->redirectView, $primaryKey, $app, $action);
			return $this->controller->redirect($url);
		}

		return $this->controller->render('single-access', [
			'model' => $model,
			'type' => $manager->getType(), // Assuming getType() gives you the type
		]);
	}

	protected function deafultRedirect($primaryKey, string $app, string $action): string {
		return Url::to([$this->redirectView, $this->redirectViewPrimaryKeyParam => $primaryKey]);
	}

}

<?php

namespace common\behaviors;

use common\helpers\Url;
use Yii;
use yii\base\Behavior;
use yii\base\Controller;
use yii\web\ForbiddenHttpException;

class IssueTypeParentIdAction extends Behavior {

	public const ISSUE_PARENT_TYPE_ALL = -1;

	public array $actions = ['index'];

	public string $param = Url::PARAM_ISSUE_PARENT_TYPE;

	/**
	 * @var \yii\web\Controller
	 * @inheritdoc
	 */
	public $owner;

	public function events(): array {
		return [
			Controller::EVENT_BEFORE_ACTION => 'beforeAction',
		];
	}

	public function beforeAction(): bool {
		if ($this->isForAction()) {
			$parentType = Yii::$app->request->get($this->param);
			if ($parentType === null) {
				$favorite = Yii::$app->user->getFavoriteIssueType();
				if ($favorite) {
					$this->owner->redirect([$this->owner->action->id, $this->param => $favorite]);
				}
			}
		}
		return true;
	}

	public static function validate(?int $parentTypeId): ?int {
		if ($parentTypeId === static::ISSUE_PARENT_TYPE_ALL) {
			return null;
		}
		if ($parentTypeId && !Yii::$app->issueTypeUser->userHasAccess(Yii::$app->user->getId(), $parentTypeId, false)) {
			throw new ForbiddenHttpException('Not Access for Type: ' . $parentTypeId);
		}
		return $parentTypeId;
	}

	protected function isForAction(): bool {
		return in_array($this->owner->action->id, $this->actions, true);
	}

	public static function urlAll(array $route = ['index']): string {
		$route[Url::PARAM_ISSUE_PARENT_TYPE] = static::ISSUE_PARENT_TYPE_ALL;
		return Url::toRoute($route);
	}
}

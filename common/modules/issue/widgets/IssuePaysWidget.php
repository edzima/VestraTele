<?php

namespace common\modules\issue\widgets;

use Closure;
use common\models\issue\IssuePay;
use common\models\user\Worker;
use Yii;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

class IssuePaysWidget extends Widget {

	/** @var IssuePay[] */
	public $models;

	public $user;

	public ?Closure $updateLink = null;
	public ?string $receivedRoute = null;
	public ?string $editRoute = null;
	public ?string $payRoute = null;
	public ?string $deleteRoute = null;

	public $editPayBtn = false;

	public function init(): void {
		parent::init();
	}

	public function run(): string {
		if (!empty($this->models)) {
			return $this->render('issue-pays', [
				'models' => $this->models,
				'widget' => $this,
				'withProvisions' => Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR)
					|| !empty($this->user),
			]);
		}
		return '';
	}

	public function getProvisionsProvider(IssuePay $pay): ?ActiveDataProvider {
		if (empty($this->user) && !Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR)) {
			return null;
		}
		$query = $pay->getProvisions()
			->with('toUser.userProfile')
			->with('fromUser.userProfile')
			->with('pay');
		if ($this->user instanceof Worker) {
			$userIds = $this->user->getAllChildesIds();
			$userIds[] = $this->user->id;
			$query->andWhere(['from_user_id' => $userIds]);
			$query->andWhere(['to_user_id' => $userIds]);
		} else {
			if (!Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR)) {
				//@todo add ::user in provisions query.
				$query->andWhere(['to_user_id' => $this->user->id]);
				$query->andWhere(['from_user_id' => $this->user->id]);
			}
		}

		return new ActiveDataProvider([
			'query' => $query,
		]);
	}

}

<?php

namespace common\modules\issue\widgets;

use common\models\issue\IssuePay;
use common\models\user\Worker;
use Yii;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

class IssuePaysWidget extends Widget {

	/** @var IssuePay[] */
	public $models;

	/**
	 * @var Worker
	 */
	public $user;

	public $editPayBtn = false;

	public function run() {
		if (!empty($this->models)) {
			return $this->render('issue-pays', [
				'models' => $this->models,
				'widget' => $this,
				'withProvisions' => Yii::$app->user->can(Worker::ROLE_BOOKKEEPER) || !empty($this->user),
			]);
		}
	}

	public function getProvisionsProvider(IssuePay $pay): ActiveDataProvider {
		$query = $pay->getProvisions()
			->with('toUser.userProfile')
			->with('fromUser.userProfile')
			->with('pay');
		if ($this->user instanceof Worker) {
			$userIds = $this->user->getAllChildesIds();
			$userIds[] = $this->user->id;
			$query->andWhere(['from_user_id' => $userIds]);
			$query->andWhere(['to_user_id' => $userIds]);
		}
		return new ActiveDataProvider([
			'query' => $query,
		]);
	}

}

<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-14
 * Time: 22:51
 */

namespace common\modules\issue\widgets;

use common\models\issue\IssuePay;
use common\models\User;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

class IssuePaysWidget extends Widget {

	/** @var IssuePay[] */
	public $models;

	/**
	 * @var User
	 */
	public $user;

	public $editPayBtn = false;

	public function run() {
		if (!empty($this->models)) {
			return $this->render('issue-pays', [
				'models' => $this->models,
				'widget' => $this,
			]);
		}
	}

	public function getProvisionsProvider(IssuePay $pay): ActiveDataProvider {
		$query = $pay->getProvisions()
			->with('toUser.userProfile')
			->with('fromUser.userProfile')
			->with('pay');
		if ($this->user instanceof User) {
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

<?php

namespace backend\modules\issue\models;

use common\models\SearchModel;
use common\models\user\User;
use common\modules\lead\models\Lead;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class IssueLeadsSearch extends Model implements SearchModel {

	public ?int $status = null;

	public function search(array $params): ActiveDataProvider {
		$query = Lead::find()
			->andFilterWhere(['status' => $this->status])
			->leftJoin(User::tableName(), [Lead::tableName() . '.email' => User::tableName() . '.email']);

		return new ActiveDataProvider([
			'query' => $query,
		]);
	}

}

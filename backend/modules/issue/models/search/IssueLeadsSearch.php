<?php

namespace backend\modules\issue\models\search;

use common\models\issue\IssueUser;
use common\models\issue\IssueUserLead;
use common\models\SearchModel;
use common\models\user\UserProfile;
use common\modules\lead\models\Lead;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class IssueLeadsSearch extends Model implements SearchModel {

	public ?int $status_id = null;
	public ?int $not_status_id = null;

	public function rules(): array {
		return [
			['status_id', 'required'],
			[['status_id', 'not_status_id'], 'integer'],
		];
	}

	public function search(array $params): ActiveDataProvider {

		$query = IssueUserLead::find()
			->select('issue_user.*, lead.id as lead_id')
			->joinWith('issue')
			->leftJoin(UserProfile::tableName() . ' UP',
				IssueUser::tableName() . '.user_id = UP.user_id'
			)
			->leftJoin(Lead::tableName(), Lead::tableName() . '.phone IS NOT NULL'
				. ' AND (UP.phone = lead.phone OR UP.phone_2 = lead.phone)');

		$this->load($params);

		$query->andFilterWhere([Lead::tableName() . '.status_id' => $this->status_id]);
		$query->andFilterWhere(['!=', Lead::tableName() . '.status_id', $this->not_status_id]);

		return new ActiveDataProvider([
			'query' => $query,
			'totalCount' => Lead::find()
				->andFilterWhere(['status_id' => $this->status_id])
				->count(),
		]);
	}

}

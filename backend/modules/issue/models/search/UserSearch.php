<?php

namespace backend\modules\issue\models\search;

use common\models\issue\IssueUser;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form of `common\models\issue\IssueUser`.
 *
 */
class UserSearch extends IssueUser {

	public $userSurname;

	/**
	 * {@inheritdoc}
	 */

	public function rules(): array {
		return [
			[['issue_id', 'user_id'], 'integer'],
			[['type', 'userSurname'], 'string'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params): ActiveDataProvider {
		$query = IssueUser::find();
		$query->joinWith('issue');
		$query->joinWith('user.userProfile');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => 'updated_at DESC',
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		if (!empty($this->userSurname)) {
			$query->with('user.userProfile');
			$query->andWhere(['like','user_profile.lastname', $this->userSurname]);
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'issue_id' => $this->issue_id,
			'user_id' => $this->user_id,
			'type' => $this->type,
		]);



		return $dataProvider;
	}

}

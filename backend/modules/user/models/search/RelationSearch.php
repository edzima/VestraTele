<?php

namespace backend\modules\user\models\search;

use common\models\user\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\user\UserRelation;

/**
 * RelationSearch represents the model behind the search form of `common\models\user\UserRelation`.
 */
class RelationSearch extends UserRelation {

	public static function getUsersNames(): array {
		return User::getSelectList(UserRelation::find()
			->select('user_id')
			->distinct()
			->column()
		);
	}

	public static function getToUsersNames(): array {
		return User::getSelectList(UserRelation::find()
			->select('to_user_id')
			->distinct()
			->column()
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'to_user_id', 'created_at', 'updated_at'], 'integer'],
			[['type'], 'string'],
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
	public function search($params) {
		$query = UserRelation::find();
		$query->with('user.userProfile');
		$query->with('toUser.userProfile');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'user_id' => $this->user_id,
			'to_user_id' => $this->to_user_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'type', $this->type]);

		return $dataProvider;
	}
}

<?php

namespace backend\modules\user\models\search;

use common\models\user\User;
use common\models\user\UserVisible;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserVisibleSearch represents the model behind the search form of `common\models\user\UserVisible`.
 */
class UserVisibleSearch extends UserVisible {

	public static function getUsersNames(): array {
		return User::getSelectList(
			UserVisible::find()
				->select('user_id')
				->distinct()
				->column(), false);
	}

	public static function getToUsersNames(): array {
		return User::getSelectList(
			UserVisible::find()
				->select('to_user_id')
				->distinct()
				->column(), false);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'to_user_id', 'status'], 'integer'],
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
		$query = UserVisible::find();

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
			'status' => $this->status,
		]);

		return $dataProvider;
	}
}

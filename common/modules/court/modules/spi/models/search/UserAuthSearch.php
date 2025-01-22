<?php

namespace common\modules\court\modules\spi\models\search;

use common\models\user\User;
use common\modules\court\modules\spi\models\auth\SpiUserAuth;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserAuthSearch represents the model behind the search form of `common\modules\court\modules\spi\models\SpiUserAuth`.
 */
class UserAuthSearch extends SpiUserAuth {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'user_id', 'created_at', 'updated_at', 'last_action_at'], 'integer'],
			[['username', 'password'], 'safe'],
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
		$query = SpiUserAuth::find();

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
			'id' => $this->id,
			'user_id' => $this->user_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'last_action_at' => $this->last_action_at,
		]);

		$query->andFilterWhere(['like', 'username', $this->username])
			->andFilterWhere(['like', 'password', $this->password]);

		return $dataProvider;
	}

	public function getUsersNames(): array {
		$ids = SpiUserAuth::find()->select('user_id')->column();
		return User::getSelectList($ids, false);
	}
}

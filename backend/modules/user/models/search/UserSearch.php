<?php

namespace backend\modules\user\models\search;

use common\models\user\query\UserQuery;
use common\models\user\SurnameSearchInterface;
use common\models\user\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User {

	public $firstname;
	public $lastname;
	public $phone;
	public $gender;
	public $region_id;
	public $city;

	protected function createQuery(): UserQuery {
		return User::find();
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'status', 'created_at', 'updated_at', 'action_at', 'gender', 'region_id'], 'integer'],
			[['username', 'email', 'ip', 'firstname', 'lastname', 'phone', 'city'], 'safe'],
			['lastname', 'string', 'min' => SurnameSearchInterface::MIN_LENGTH],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios(): array {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied.
	 *
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search(array $params): ActiveDataProvider {
		$query = $this->createQuery();
		$query->joinWith('userProfile');
		$query->joinWith('addresses.address.city');
		$query->joinWith('traits');
		$query->with('addresses.address.city');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pagesize' => 30,
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'user.id' => $this->id,
			'status' => $this->status,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'action_at' => $this->action_at,
			'profile.gender' => $this->gender,
		]);

		$query->andFilterWhere(['like', 'username', $this->username])
			->andFilterWhere(['like', 'user_profile.firstname', $this->firstname])
			->andFilterWhere(['like', 'user_profile.lastname', $this->lastname])
			->andFilterWhere(['like', 'user_profile.phone', $this->phone])
			->andFilterWhere(['like', 'email', $this->email])
			->andFilterWhere(['like', 'teryt_simc.region_id', $this->region_id])
			->andFilterWhere(['like', 'teryt_simc.name', $this->city])
			->andFilterWhere(['like', 'ip', $this->ip]);

		return $dataProvider;
	}
}

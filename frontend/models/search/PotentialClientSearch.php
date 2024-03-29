<?php

namespace frontend\models\search;

use common\models\AddressSearch;
use common\models\PotentialClient;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PotentialClientSearch represents the model behind the search form of `common\models\PotentialClient`.
 */
class PotentialClientSearch extends PotentialClient {

	public const SCENARIO_REQUIRED_FIELDS = 'required-fields';
	private ?AddressSearch $addressSearch = null;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'city_id', 'status', '!owner_id'], 'integer'],
			[['firstname', 'lastname', 'details', 'birthday', 'created_at', 'updated_at'], 'safe'],
			[['firstname', 'lastname', 'birthday'], 'required', 'on' => static::SCENARIO_REQUIRED_FIELDS],
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
		$query = PotentialClient::find();
		$query->joinWith('city');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);
		$this->getAddressSearch()->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			$query->where('0=1');
			return $dataProvider;
		}

		$this->getAddressSearch()->applySearch($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'city_id' => $this->city_id,
			'birthday' => $this->birthday,
			'owner_id' => $this->owner_id,
			'status' => $this->status,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'firstname', $this->firstname])
			->andFilterWhere(['like', 'lastname', $this->lastname])
			->andFilterWhere(['like', 'details', $this->details]);

		return $dataProvider;
	}

	public function getAddressSearch(): AddressSearch {
		if ($this->addressSearch === null) {
			$this->addressSearch = new AddressSearch();
		}
		return $this->addressSearch;
	}
}

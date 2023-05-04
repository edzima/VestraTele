<?php

namespace backend\models\search;

use common\models\AddressSearch;
use common\models\user\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PotentialClient;

/**
 * PotentialClientSearch represents the model behind the search form of `common\models\PotentialClient`.
 */
class PotentialClientSearch extends PotentialClient {

	private ?AddressSearch $addressSearch = null;

	public static function getOwnersNames(): array {
		return User::getSelectList(
			PotentialClient::find()
				->select('owner_id')
				->distinct()
				->column()
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'city_id', 'status', 'owner_id'], 'integer'],
			[['firstname', 'lastname', 'details', 'birthday', 'created_at', 'updated_at', 'phone'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios(): array {
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
	public function search(array $params): ActiveDataProvider {
		$query = PotentialClient::find();

		$query->with('owner.userProfile');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);
		$this->getAddressSearch()->load($params);
		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$this->getAddressSearch()->applySearch($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'city_id' => $this->city_id,
			'birthday' => $this->birthday,
			'status' => $this->status,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'owner_id' => $this->owner_id,
		]);

		if (!empty($this->phone)) {
			$query->withPhoneNumber($this->phone);
		}

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

<?php

namespace common\models\issue;

use edzima\teryt\models\Region;
use edzima\teryt\models\Simc;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * IssueMeetSearch represents the model behind the search form of `common\models\issue\IssueMeet`.
 */
class IssueMeetSearch extends IssueMeet {

	protected const EMPTY_CAMPAIGN_ID = -1;

	public $withArchive = false;

	public $cityName;
	public $regionId;

	public $created_at_from;
	public $created_at_to;
	public $date_at_from;
	public $date_at_to;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'type_id', 'regionId', 'agent_id', 'status', 'campaign_id'], 'integer'],
			['status', 'in', 'range' => array_keys(static::getStatusNames($this->withArchive))],
			[
				[
					'phone', 'client_name', 'client_surname', 'created_at', 'updated_at',
					'date_at', 'date_at_from', 'date_at_to', 'details', 'cityName', 'created_at_from', 'created_at_to',
				], 'safe',
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios(): array {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'created_at_from' => 'Data leada (od)',
			'created_at_to' => 'Data leada (do)',
			'date_at_from' => 'Data Wysyłki/Spotkania/Akcji ',
			'date_at_to' => 'Koniec Wysyłki/Spotkania/Akcji',
		]);
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search(array $params) {
		$query = IssueMeet::find();
		//@todo add with Address
		$query
			->with('addresses.address.city')
			->with('type')
			->with('campaign')
			->with(['agent.userProfile']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => ['updated_at' => SORT_DESC],
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
			'id' => $this->id,
			'type_id' => $this->type_id,
			'agent_id' => $this->agent_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'date_at' => $this->date_at,
			'status' => $this->status,
		]);

		$this->filterCampaign($query);
		$this->filterAddress($query);

		$query->andFilterWhere(['like', 'phone', $this->phone])
			->andFilterWhere(['like', 'client_name', $this->client_name])
			->andFilterWhere(['like', 'client_surname', $this->client_surname])
			->andFilterWhere(['like', 'details', $this->details])
			->andFilterWhere(['>=', 'created_at', $this->created_at_from])
			->andFilterWhere(['<=', 'created_at', $this->created_at_to])
			->andFilterWhere(['>=', 'date_at', $this->date_at_from])
			->andFilterWhere(['<=', 'date_at', $this->date_at_to]);

		return $dataProvider;
	}

	private function filterAddress(ActiveQuery $query): void {
		if (!empty($this->regionId) || !empty($this->cityName)) {
			$query->joinWith('addresses.address.city');
			$query->andFilterWhere([Simc::tableName() . '.region_id' => $this->regionId]);
			$query->andFilterWhere(['like', Simc::tableName() . '.name', $this->cityName]);
		}
	}

	private function filterCampaign(ActiveQuery $query): void {
		if (!empty($this->campaign_id)) {
			if ((int) $this->campaign_id === static::EMPTY_CAMPAIGN_ID) {
				$query->andWhere('campaign_id IS NULL');
			} else {
				$query->andWhere(['campaign_id' => $this->campaign_id]);
			}
		}
	}

	public static function getCampaignNames(): array {
		$names = parent::getCampaignNames();
		$names[static::EMPTY_CAMPAIGN_ID] = 'Własna';
		return $names;
	}

	public static function getRegionsNames(): array {
		return Region::getNames();
	}

	public static function getStatusNames(bool $withArchive = false): array {
		$names = parent::getStatusNames();
		if (!$withArchive) {
			unset($names[static::STATUS_ARCHIVE]);
		}
		return $names;
	}
}

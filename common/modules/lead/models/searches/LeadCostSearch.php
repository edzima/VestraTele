<?php

namespace common\modules\lead\models\searches;

use common\helpers\ArrayHelper;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadCost;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * LeadCostSearch represents the model behind the search form of `common\modules\lead\models\LeadCost`.
 */
class LeadCostSearch extends LeadCost {

	public const SCENARIO_USER = 'user';

	public ?int $userId = null;

	public string $fromAt = '';
	public string $toAt = '';
	public string $valueMin = '';
	public string $valueMax = '';

	private array $campaignNames = [];

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			['!userId', 'required', 'on' => static::SCENARIO_USER],
			[['id', 'campaign_id'], 'integer'],
			[['value', 'valueMin', 'valueMax'], 'number'],
			[['date_at', 'created_at', 'updated_at', 'fromAt', 'toAt'], 'safe'],
		];
	}

	public function attributeLabels(): array {
		return array_merge(
			parent::attributeLabels(), [
				'fromAt' => Yii::t('lead', 'From At'),
				'toAt' => Yii::t('lead', 'To At'),
				'valueMin' => Yii::t('lead', 'Value (min)'),
				'valueMax' => Yii::t('lead', 'Value (max)'),
			]
		);
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
	public function search(array $params) {
		$query = LeadCost::find();

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

		$this->applyUserFilter($query);
		$this->applyDateFilter($query);
		$this->applyValueFilter($query);
		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'campaign_id' => $this->campaign_id,
			'value' => $this->value,
			'date_at' => $this->date_at,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		return $dataProvider;
	}

	public function getCampaignNames(): array {
		if (empty($this->campaignNames)) {
			$query = LeadCost::find();
			if ($this->userId) {
				$this->applyUserFilter($query);
			}
			$query->distinct();
			$query->select('campaign_id');
			$this->campaignNames = ArrayHelper::map(LeadCampaign::find()
				->andWhere(['id' => $query])
				->all(),
				'id',
				$this->userId ? 'name' : 'nameWithOwner'
			);
		}
		return $this->campaignNames;
	}

	private function applyUserFilter(ActiveQuery $query): void {
		$query->joinWith('campaign');
		$query->andFilterWhere([LeadCampaign::tableName() . '.owner_id' => $this->userId]);
	}

	private function applyDateFilter(ActiveQuery $query) {
		if (!empty($this->fromAt)) {
			$query->andWhere(['>=', LeadCost::tableName() . '.date_at', $this->fromAt]);
		}
		if (!empty($this->toAt)) {
			$query->andWhere(['<=', LeadCost::tableName() . '.date_at', $this->toAt]);
		}
	}

	private function applyValueFilter(ActiveQuery $query) {
		if (!empty($this->valueMin)) {
			$query->andWhere(['>=', LeadCost::tableName() . '.value', $this->valueMin]);
		}
		if (!empty($this->valueMax)) {
			$query->andWhere(['<=', LeadCost::tableName() . '.value', $this->valueMax]);
		}
	}

}
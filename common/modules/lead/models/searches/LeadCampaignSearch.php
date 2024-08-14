<?php

namespace common\modules\lead\models\searches;

use common\models\user\User;
use common\modules\lead\models\LeadCampaign;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LeadCampaignSearch represents the model behind the search form of `common\modules\lead\models\LeadCampaign`.
 */
class LeadCampaignSearch extends LeadCampaign {

	public const WITHOUT_OWNER = -1;

	public static function getOwnersNames(): array {
		$owners = [self::WITHOUT_OWNER => Yii::t('lead', '---Without Owner---')];
		return array_merge($owners, User::getSelectList(
			LeadCampaign::find()
				->andWhere('owner_id IS NOT NULL')
				->distinct()
				->select('owner_id')
				->column()
			, false));
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'sort_index', 'owner_id', 'is_active', 'leads_count'], 'integer'],
			['!owner_id', 'required', 'on' => static::SCENARIO_OWNER],
			[['name'], 'safe'],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
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
	public function search(array $params): ActiveDataProvider {
		$query = LeadCampaign::find();

		$query->joinWith('leads');
		$query->joinWith('costs c');
		$query->select([
			LeadCampaign::tableName() . '.*',
			'count(lead.id) as leads_count',
			'sum(c.value) as cost_value',
		]);
		$query->groupBy(LeadCampaign::tableName() . '.id');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		$dataProvider->sort->attributes['leads_count'] = [
			'asc' => ['leads_count' => SORT_ASC],
			'desc' => ['leads_count' => SORT_DESC],
		];
		$dataProvider->sort->attributes['totalCostSumValue'] = [
			'asc' => ['cost_value' => SORT_ASC],
			'desc' => ['cost_value' => SORT_DESC],
		];

		$this->load($params);

		if (!$this->validate()) {
			$query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			LeadCampaign::tableName() . '.id' => $this->id,
			LeadCampaign::tableName() . '.sort_index' => $this->sort_index,
			LeadCampaign::tableName() . '.owner_id' => $this->owner_id,
			LeadCampaign::tableName() . '.is_active' => $this->is_active,
			LeadCampaign::tableName() . '.type' => $this->type,
			LeadCampaign::tableName() . '.details' => $this->details,
		]);

		$query->andFilterWhere([
			'like', LeadCampaign::tableName() . '.name', $this->name,
		]);

		return $dataProvider;
	}
}

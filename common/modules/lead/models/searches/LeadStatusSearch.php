<?php

namespace common\modules\lead\models\searches;

use common\helpers\ArrayHelper;
use common\modules\lead\models\LeadStatus;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LeadStatusSearch represents the model behind the search form of `common\modules\lead\models\LeadStatus`.
 */
class LeadStatusSearch extends LeadStatus {

	public static function getChartGroupsNames(): array {
		return ArrayHelper::map(static::getModels(), 'chart_group', 'chart_group');
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'sort_index', 'market_status', 'hours_deadline', 'hours_deadline_warning'], 'integer'],
			[['short_report', 'show_report_in_lead_index', 'not_for_dialer'], 'boolean'],
			[['name', 'description', 'chart_group', 'calendar_background', 'chart_color'], 'safe'],
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
		$query = LeadStatus::find();

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
			'market_status' => $this->market_status,
			'sort_index' => $this->sort_index,
			'short_report' => $this->short_report,
			'not_for_dialer' => $this->not_for_dialer,
			'hours_deadline' => $this->hours_deadline,
			'chart_group' => $this->chart_group,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'description', $this->description]);

		return $dataProvider;
	}
}

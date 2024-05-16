<?php

namespace backend\modules\issue\models\search;

use backend\modules\issue\models\IssueStage;
use common\models\issue\IssueType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssueStageSearch represents the model behind the search form of `common\models\issue\IssueStage`.
 */
class IssueStageSearch extends IssueStage {

	public $typesFilter;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'days_reminder'], 'integer'],
			[['name', 'short_name'], 'safe'],
			['typesFilter', 'in', 'range' => array_keys(IssueType::getTypesNames()), 'allowArray' => true],
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
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search(array $params): ActiveDataProvider {
		$query = IssueStage::find();
		$query->joinWith('types');
		$query->with('stageTypes');
		$query->groupBy('id');

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

		if (!empty($this->typesFilter)) {
			$query->andWhere([IssueType::tableName() . '.id' => $this->typesFilter]);
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'days_reminder' => $this->days_reminder,
		]);

		$query->andFilterWhere(['like', IssueStage::tableName() . '.name', $this->name])
			->andFilterWhere(['like', IssueStage::tableName() . '.short_name', $this->short_name]);

		$query->addOrderBy('posi');

		return $dataProvider;
	}
}

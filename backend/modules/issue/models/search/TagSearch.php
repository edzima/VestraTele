<?php

namespace backend\modules\issue\models\search;

use common\models\issue\IssueTag;
use common\models\issue\IssueTagLink;
use common\models\issue\IssueTagType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TagSearch represents the model behind the search form of `common\models\issue\IssueTag`.
 */
class TagSearch extends IssueTag {



	public static function getTypesNames(): array {
		return IssueTagType::getTypesNames();
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'is_active','issuesCount'], 'integer'],
			[['is_active'], 'default', 'value' => null],
			[['name', 'description', 'type'], 'safe'],
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
		$query = IssueTag::find();
		$query->joinWith('issueTagLinks');
		$query->groupBy(IssueTag::tableName() . '.id');
		$query->addSelect(['*','COUNT(' . IssueTagLink::tableName() . '.issue_id' . ') as issuesCount']);
		$query->with([
			'tagType',
		]);

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'attributes'=> [
					'name',
					'description',
					'issuesCount',
				]
			]
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
			'is_active' => $this->is_active,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'type', $this->type])
			->andFilterWhere(['like', 'description', $this->description]);

		return $dataProvider;
	}
}

<?php

namespace common\modules\file\models\search;

use common\modules\file\models\FileType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FileTypeSearch represents the model behind the search form of `common\modules\file\models\FileType`.
 */
class FileTypeSearch extends FileType {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'is_active'], 'integer'],
			[['name', 'visibility', 'validator_config'], 'safe'],
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
	public function search($params) {
		$query = FileType::find();

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
			'is_active' => $this->is_active,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'visibility', $this->visibility])
			->andFilterWhere(['like', 'validator_config', $this->validator_config]);

		return $dataProvider;
	}
}

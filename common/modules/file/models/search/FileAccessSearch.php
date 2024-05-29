<?php

namespace common\modules\file\models\search;

use common\modules\file\models\FileAccess;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FileAccessSearch represents the model behind the search form of `common\modules\file\models\FileAccess`.
 */
class FileAccessSearch extends FileAccess {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['file_id', 'user_id'], 'integer'],
			[['access'], 'safe'],
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
		$query = FileAccess::find();

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
			'file_id' => $this->file_id,
			'user_id' => $this->user_id,
		]);

		$query->andFilterWhere(['like', 'access', $this->access]);

		return $dataProvider;
	}
}

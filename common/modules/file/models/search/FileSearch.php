<?php

namespace common\modules\file\models\search;

use common\modules\file\models\File;
use common\modules\file\models\FileType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FileSearch represents the model behind the search form of `common\modules\file\models\File`.
 */
class FileSearch extends File {

	public static function getFileTypesNames(): array {
		return FileType::getNames(false);
	}

	public static function getTypesNames(): array {
		return File::find()
			->select('type')
			->asArray()
			->distinct()
			->groupBy('type')
			->indexBy('type')
			->column();
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'size', 'file_type_id', 'owner_id'], 'integer'],
			[['name', 'hash', 'type', 'mime', 'created_at', 'updated_at'], 'safe'],
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
		$query = File::find();

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
			'size' => $this->size,
			'file_type_id' => $this->file_type_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'owner_id' => $this->owner_id,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'hash', $this->hash])
			->andFilterWhere(['like', 'type', $this->type])
			->andFilterWhere(['like', 'mime', $this->mime]);

		return $dataProvider;
	}
}

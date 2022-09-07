<?php

namespace common\models\hint\searches;

use common\models\hint\HintCitySource;
use common\models\hint\HintSource;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\helpers\ArrayHelper;

/**
 * HintCitySearch represents the model behind the search form of `common\models\hint\HintCity`.
 */
class HintCitySourceSearch extends HintCitySource {

	public $user_id;
	public $hintType;
	public $hintStatus;
	public $hintCityName;

	public function rules(): array {
		return [
			[['source_id', 'user_id'], 'integer'],
			[['rating', 'phone', 'hintType', 'hintStatus', 'hintCityName', 'details', 'created_at', 'updated_at', 'status'], 'string'],
		];
	}

	public static function getSourcesNames(): array {
		return ArrayHelper::map(
			HintSource::find()
				->joinWith('hintCitySources')
				->groupBy('id')
				->all(),
			'id', 'name');
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search(array $params): DataProviderInterface {
		$query = HintCitySource::find();
		$query->joinWith('hint H');

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

		$query->andFilterWhere([
			HintCitySource::tableName() . '.source_id' => $this->source_id,
			HintCitySource::tableName() . '.rating' => $this->rating,
			HintCitySource::tableName() . '.status' => $this->status,
			'H.user_id' => $this->user_id,
			'H.type' => $this->hintType,
			'H.status' => $this->hintStatus,
		]);

		$query->andFilterWhere([
			'like', HintCitySource::tableName() . '.details', $this->details,
		]);

		return $dataProvider;
	}

}

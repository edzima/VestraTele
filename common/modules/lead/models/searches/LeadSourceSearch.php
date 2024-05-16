<?php

namespace common\modules\lead\models\searches;

use common\models\user\User;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LeadSourceSearch represents the model behind the search form of `common\modules\lead\models\LeadSource`.
 */
class LeadSourceSearch extends LeadSource {

	public static function getOwnersNames(): array {
		return User::getSelectList(
			LeadSource::find()
				->select('owner_id')
				->distinct()
				->column(),
			false
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			['!owner_id', 'required', 'on' => static::SCENARIO_OWNER],
			[['id', 'sort_index', 'owner_id', 'type_id', 'call_page_widget_id'], 'integer'],
			[['is_active'], 'boolean'],
			[['is_active'], 'default', 'value' => null],
			[['name', 'url', 'phone', 'dialer_phone', 'sms_push_template'], 'safe'],
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
		$query = LeadSource::find();

		$query->with(['leadType', 'owner']);

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			$query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'sort_index' => $this->sort_index,
			'owner_id' => $this->owner_id,
			'is_active' => $this->is_active,
			'type_id' => $this->type_id,
			'call_page_widget_id' => $this->call_page_widget_id,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'phone', $this->phone])
			->andFilterWhere(['like', 'dialer_phone', $this->dialer_phone])
			->andFilterWhere(['like', 'sms_push_template', $this->sms_push_template])
			->andFilterWhere(['like', 'url', $this->url]);

		return $dataProvider;
	}

	public static function getTypesNames(): array {
		return LeadType::getNames();
	}
}

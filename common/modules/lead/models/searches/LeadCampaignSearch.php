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
			[['id', 'sort_index', 'owner_id', 'is_active'], 'integer'],
			['!owner_id', 'required', 'on' => static::SCENARIO_OWNER],
			[['name'], 'safe'],
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
		]);

		$query->andFilterWhere(['like', 'name', $this->name]);

		return $dataProvider;
	}
}

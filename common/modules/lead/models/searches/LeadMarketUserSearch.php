<?php

namespace common\modules\lead\models\searches;

use common\models\user\User;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * LeadMarketUserSearch represents the model behind the search form of `common\modules\lead\models\LeadMarketUser`.
 */
class LeadMarketUserSearch extends LeadMarketUser {

	const SCENARIO_USER_MARKET = 'user-market';
	public $marketStatus;
	public $marketCreatorId;

	public $withoutArchiveMarket = true;

	public const SCENARIO_USER = 'user';

	public static function getUsersNames(): array {
		return User::getSelectList(
			LeadMarketUser::find()
				->select('user_id')
				->distinct()
				->column(),
			false);
	}

	public static function getMarketCreatorsNames() {
		return LeadMarketSearch::getCreatorsNames();
	}

	public static function getMarketStatusesNames(): array {
		return LeadMarket::getStatusesNames();
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			['!marketCreatorId', 'required', 'on' => static::SCENARIO_USER_MARKET],
			['!user_id', 'required', 'on' => static::SCENARIO_USER],
			[['market_id', 'days_reservation', 'status', 'user_id', 'marketCreatorId', 'marketStatus'], 'integer'],
			[['details'], 'string'],
			[['withoutArchiveMarket'], 'boolean'],
			[['created_at', 'updated_at', 'reserved_at'], 'safe'],
		];
	}

	public function attributeLabels(): array {
		return parent::attributeLabels() + [
				'withoutArchiveMarket' => Yii::t('lead', 'Without Archives'),
				'marketStatus' => Yii::t('lead', 'Market Status'),
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
	public function search(array $params) {
		$query = LeadMarketUser::find();
		$query->with('market');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		if ($this->scenario !== self::SCENARIO_USER) {
			$query->joinWith('user.userProfile');
		}

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			$query->where('0=1');
			return $dataProvider;
		}

		$this->applyMarketStatusFilter($query);
		$this->applyMarketCreatorFilter($query);
		$this->applyWithoutArchivedMarketFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			LeadMarketUser::tableName() . '.market_id' => $this->market_id,
			LeadMarketUser::tableName() . '.user_id' => $this->user_id,
			LeadMarketUser::tableName() . '.status' => $this->status,
			LeadMarketUser::tableName() . '.created_at' => $this->created_at,
			LeadMarketUser::tableName() . '.updated_at' => $this->updated_at,
			LeadMarketUser::tableName() . '.reserved_at' => $this->reserved_at,
		]);

		return $dataProvider;
	}

	private function applyMarketStatusFilter(ActiveQuery $query): void {
		if (!empty($this->marketStatus)) {
			$query->joinWith('market');
			$query->andWhere([LeadMarket::tableName() . '.status' => $this->marketStatus]);
		}
	}

	private function applyMarketCreatorFilter(ActiveQuery $query): void {
		if (!empty($this->marketCreatorId)) {
			$query->joinWith('market');
			$query->andWhere([LeadMarket::tableName() . '.creator_id' => $this->marketCreatorId]);
		}
	}

	private function applyWithoutArchivedMarketFilter(ActiveQuery $query): void {
		if ($this->withoutArchiveMarket) {
			$query->joinWith('market');
			$query->andWhere(['!=', LeadMarket::tableName() . '.status', LeadMarket::STATUS_ARCHIVED]);
		}
	}
}

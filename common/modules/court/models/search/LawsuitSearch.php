<?php

namespace common\modules\court\models\search;

use common\helpers\ArrayHelper;
use common\models\issue\Issue;
use common\models\user\query\UserProfileQuery;
use common\modules\court\models\Court;
use common\modules\court\models\Lawsuit;
use common\modules\court\models\query\LawsuitQuery;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * CourtHearingSearch represents the model behind the search form of `common\modules\court\models\CourtHearing`.
 */
class LawsuitSearch extends Lawsuit {

	public $issue_id;
	public $customer;
	public $court_type;

	public $issueUserId;

	public const SCENARIO_ISSUE_USER = 'issue_user_id';

	public function attributeLabels(): array {
		return parent::attributeLabels() + [
				'court_type' => Yii::t('court', 'Type'),
			];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			['!issueUserId', 'required', 'on' => self::SCENARIO_ISSUE_USER],
			[['id', 'court_id', 'creator_id', 'issue_id'], 'integer'],
			[['is_appeal'], 'default', 'value' => null],
			[['customer', 'signature_act', 'room', 'due_at', 'details', 'created_at', 'updated_at', 'location', 'presence_of_the_claimant', 'court_type', 'url'], 'safe'],
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
		$query = Lawsuit::find();
		$query->joinWith('issues');
		$query->joinWith('court');
		$query->with('issues.customer.userProfile');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			$query->where('0=1');
			return $dataProvider;
		}

		$this->applyIssueUserFilter($query);
		$this->applyCustomerFileter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'court_id' => $this->court_id,
			'due_at' => $this->due_at,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'creator_id' => $this->creator_id,
			'location' => $this->location,
			'presence_of_the_claimant' => $this->presence_of_the_claimant,
			'is_appeal' => $this->is_appeal,
			Court::tableName() . '.type' => $this->court_type,
		]);

		$query->andFilterWhere(['like', 'signature_act', $this->signature_act])
			->andFilterWhere(['like', 'details', $this->details])
			->andFilterWhere(['like', 'room', $this->room])
			->andFilterWhere(['like', 'url', $this->url])
			->andFilterWhere(['like', Issue::tableName() . '.id', $this->issue_id . '%', false]);

		$query->groupBy(Lawsuit::tableName() . '.id');

		return $dataProvider;
	}

	public static function getCourtsNames(): array {

		return ArrayHelper::map(
			Court::find()
				->andWhere([
					'id' => Lawsuit::find()
						->select('court_id')
						->distinct()
						->column(),
				])
				->asArray()
				->all(),
			'id', 'name');
	}

	private function applyCustomerFileter(ActiveQuery $query) {
		if (!empty($this->customer)) {
			$query->joinWith([
				'issues.customer.userProfile' => function (UserProfileQuery $query) {
					$query->withFullName($this->customer);
				},
			]);
		}
	}

	public static function getCourtTypeNames(): array {
		return Court::getTypesNames();
	}

	private function applyIssueUserFilter(LawsuitQuery $query): void {
		if (!empty($this->issueUserId)) {
			$query->usersIssues((array) $this->issueUserId);
		}
	}
}

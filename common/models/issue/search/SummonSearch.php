<?php

namespace common\models\issue\search;

use common\models\issue\Summon;
use common\models\issue\SummonType;
use common\models\query\PhonableQuery;
use common\models\SearchModel;
use common\models\user\CustomerSearchInterface;
use common\models\user\query\UserQuery;
use common\models\user\User;
use common\validators\PhoneValidator;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\QueryInterface;

/**
 * SummonSearch represents the model behind the search form of `common\models\issue\Summon`.
 */
class SummonSearch extends Summon implements
	CustomerSearchInterface,
	SearchModel {

	public string $customerLastname = '';
	public string $customerPhone = '';

	protected const SUMMON_ALIAS = 'S';

	public static function getTypesNames(): array {
		return SummonType::getNames();
	}

	public static function getOwnersNames(): array {
		return User::getSelectList(Summon::find()
			->select('owner_id')
			->distinct()
			->column()
		);
	}

	public static function getContractorsNames(): array {
		return User::getSelectList(Summon::find()
			->select('contractor_id')
			->distinct()
			->column()
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'type_id', 'status', 'created_at', 'updated_at', 'realized_at', 'start_at', 'deadline_at', 'issue_id', 'owner_id', 'contractor_id'], 'integer'],
			[['title'], 'safe'],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
			['customerPhone', PhoneValidator::class],
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
	 * @return ActiveDataProvider
	 */
	public function search(array $params): ActiveDataProvider {
		$query = Summon::find();
		$query->alias(static::SUMMON_ALIAS);
		$query->joinWith([
			'issue.customer C' => function (UserQuery $query) {
				$query->joinWith('userProfile CP');
			},
		]);
		$query->with('type');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		$dataProvider->sort->defaultOrder = [
			'start_at' => SORT_DESC,
		];

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$this->applyCustomerSurnameFilter($query);
		$this->applyCustomerPhoneFilter($query);
		// grid filtering conditions
		$query->andFilterWhere([
			static::SUMMON_ALIAS . '.id' => $this->id,
			static::SUMMON_ALIAS . '.type_id' => $this->type_id,
			static::SUMMON_ALIAS . '.status' => $this->status,
			static::SUMMON_ALIAS . '.created_at' => $this->created_at,
			static::SUMMON_ALIAS . '.updated_at' => $this->updated_at,
			static::SUMMON_ALIAS . '.start_at' => $this->start_at,
			static::SUMMON_ALIAS . '.realized_at' => $this->realized_at,
			static::SUMMON_ALIAS . '.owner_id' => $this->owner_id,
			static::SUMMON_ALIAS . '.contractor_id' => $this->contractor_id,
		]);

		$query->andFilterWhere(['like', static::SUMMON_ALIAS . '.issue_id', $this->issue_id]);
		$query->andFilterWhere(['like', static::SUMMON_ALIAS . '.title', $this->title]);

		return $dataProvider;
	}

	public function applyCustomerSurnameFilter(QueryInterface $query): void {
		if (!empty($this->customerLastname)) {
			$query->andWhere(['like', 'CP.lastname', $this->customerLastname . '%', false]);
		}
	}

	private function applyCustomerPhoneFilter(ActiveQuery $query): void {
		if (!empty($this->customerPhone)) {
			$query->joinWith([
				'issue.customer.userProfile CP' => function (PhonableQuery $query) {
					$query->withPhoneNumber($this->customerPhone);
				},
			]);
		}
	}
}

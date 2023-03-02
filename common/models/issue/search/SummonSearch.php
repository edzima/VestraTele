<?php

namespace common\models\issue\search;

use common\helpers\ArrayHelper;
use common\helpers\Html;
use common\helpers\Url;
use common\models\issue\Issue;
use common\models\issue\IssueType;
use common\models\issue\Summon;
use common\models\issue\SummonDoc;
use common\models\issue\SummonType;
use common\models\query\PhonableQuery;
use common\models\SearchModel;
use common\models\user\CustomerSearchInterface;
use common\models\user\query\UserQuery;
use common\models\user\User;
use common\validators\PhoneValidator;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\QueryInterface;

/**
 * SummonSearch represents the model behind the search form of `common\models\issue\Summon`.
 */
class SummonSearch extends Summon implements
	CustomerSearchInterface,
	IssueParentTypeSearchable,
	SearchModel {

	public ?int $issueParentTypeId = null;
	public $doc_types_ids;

	public string $customerLastname = '';
	public string $customerPhone = '';

	protected const SUMMON_ALIAS = 'S';

	public static function getTypesNames(): array {
		return SummonType::getNamesWithShort();
	}

	public static function getDocTypesNames(): array {
		return SummonDoc::getNames();
	}

	public function getSummonTypeNavItems(): array {
		$typeQuery = Summon::find()
			->select([Summon::tableName() . '.type_id', 'count(*) as typeCount'])
			->groupBy('type_id')
			->asArray();

		if (!empty($this->user_id)) {
			$typeQuery->user($this->user_id);
		}

		if ($this->getIssueParentType()) {
			$this->applyIssueParentTypeFilter($typeQuery);
		}
		$types = $typeQuery
			->all();
		$typesItems = [];
		foreach ($types as $row) {
			$typeId = (int) $row['type_id'];
			$count = (int) $row['typeCount'];
			$typeName = SummonType::getNames()[$typeId];
			$typesItems[] = [
				'label' => "$typeName ($count)",
				'url' => ['index', Html::getInputName($this, 'type_id') => $typeId, Url::PARAM_ISSUE_PARENT_TYPE => $this->issueParentTypeId],
				'active' => (int) $this->type_id === $typeId,
			];
		}
		$typesItems[] = [
			'label' => Yii::t('common', 'All'),
			'url' => ['index', Url::PARAM_ISSUE_PARENT_TYPE => $this->issueParentTypeId],
			'active' => empty($this->type_id),
		];
		return $typesItems;
	}

	public function getOwnersNames(): array {
		return User::getSelectList(Summon::find()
			->select('owner_id')
			->distinct()
			->column(),
			false
		);
	}

	public function getContractorsNames(int $ownerId = null): array {
		$query = Summon::find()
			->select('contractor_id')
			->distinct();
		if ($ownerId) {
			$query->andWhere(['owner_id' => $ownerId]);
		}
		$this->applyIssueParentTypeFilter($query);
		$ids = $query->column();
		return User::getSelectList($ids,
			false
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'type_id', 'status', 'created_at', 'updated_at', 'realized_at', 'start_at', 'deadline_at', 'issue_id', 'owner_id', 'contractor_id'], 'integer'],
			[['title'], 'safe'],
			['doc_types_ids', 'in', 'range' => array_keys(static::getDocTypesNames()), 'allowArray' => true],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
			['customerPhone', PhoneValidator::class],
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
		$query->with('docsLink.doc');
		$query->with('owner.userProfile');
		$query->with('contractor.userProfile');
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

		if (!empty($this->doc_types_ids)) {
			$query->joinWith('docs');
			$query->andFilterWhere([SummonDoc::tableName() . '.id' => $this->doc_types_ids]);
		}

		$this->applyCustomerNameFilter($query);
		$this->applyCustomerPhoneFilter($query);
		$this->applyIssueParentTypeFilter($query);
		// grid filtering conditions
		$query->andFilterWhere([
			static::SUMMON_ALIAS . '.id' => $this->id,
			static::SUMMON_ALIAS . '.issue_id' => $this->issue_id,
			static::SUMMON_ALIAS . '.type_id' => $this->type_id,
			static::SUMMON_ALIAS . '.status' => $this->status,
			static::SUMMON_ALIAS . '.created_at' => $this->created_at,
			static::SUMMON_ALIAS . '.updated_at' => $this->updated_at,
			static::SUMMON_ALIAS . '.start_at' => $this->start_at,
			static::SUMMON_ALIAS . '.realized_at' => $this->realized_at,
			static::SUMMON_ALIAS . '.owner_id' => $this->owner_id,
			static::SUMMON_ALIAS . '.contractor_id' => $this->contractor_id,
		]);

		$query->andFilterWhere(['like', static::SUMMON_ALIAS . '.title', $this->title]);

		return $dataProvider;
	}

	public function applyCustomerNameFilter(QueryInterface $query): void {
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

	public function applyIssueParentTypeFilter(ActiveQuery $query): void {
		$parentType = $this->getIssueParentType();
		if ($parentType) {
			$childs = ArrayHelper::getColumn($parentType->childs, 'id');
			$query->joinWith('issue');
			$query->andFilterWhere([Issue::tableName() . '.type_id' => $childs]);
		}
	}

	public function getIssueParentType(): ?IssueType {
		if ($this->issueParentTypeId) {
			return IssueType::get($this->issueParentTypeId);
		}
		return null;
	}

}

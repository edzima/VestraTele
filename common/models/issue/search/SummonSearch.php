<?php

namespace common\models\issue\search;

use common\helpers\Html;
use common\helpers\Url;
use common\models\issue\Issue;
use common\models\issue\IssueStage;
use common\models\issue\IssueTag;
use common\models\issue\IssueTagLink;
use common\models\issue\IssueType;
use common\models\issue\query\IssueQuery;
use common\models\issue\query\SummonQuery;
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
	IssueMainTypeSearchable,
	IssueStageSearchable,
	SearchModel {

	public ?int $issueParentTypeId = null;
	public $doc_types_ids;

	public string $customerLastname = '';
	public string $customerPhone = '';

	public $tagsIds;

	public $excludedTagsIds;

	public $issueStageId;

	public $excludedIssueStagesIds;

	protected const SUMMON_ALIAS = 'S';

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'type_id', 'status', 'created_at', 'updated_at', 'realized_at', 'start_at', 'deadline_at', 'issue_id', 'owner_id', 'contractor_id'], 'integer'],
			[['title'], 'safe'],
			['doc_types_ids', 'in', 'range' => array_keys(static::getDocTypesNames()), 'allowArray' => true],
			[['issueStageId', 'excludedIssueStagesIds'], 'in', 'range' => array_keys($this->getIssueStagesNames()), 'allowArray' => true],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
			['customerPhone', PhoneValidator::class],
			[['excludedTagsIds', 'tagsIds'], 'in', 'range' => array_keys(IssueTag::getModels()), 'allowArray' => true],

		];
	}

	public static function getTypesNames(): array {
		return SummonType::getNamesWithShort();
	}

	public static function getDocTypesNames(): array {
		return SummonDoc::getNames();
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'tagsIds' => Yii::t('issue', 'Tags'),
			'excludedTagsIds' => Yii::t('issue', 'Excluded tags'),
			'excludedIssueStagesIds' => Yii::t('issue', 'Excluded stages'),
		]);
	}

	public function getSummonTypeNavItems(int $parentTypeId = null): array {
		if ($parentTypeId === null) {
			$parentTypeId = Yii::$app->request->get(Url::PARAM_ISSUE_PARENT_TYPE);
		}
		$typeQuery = Summon::find()
			->select([Summon::tableName() . '.type_id', 'count(*) as typeCount'])
			->groupBy('type_id')
			->active()
			->asArray();

		if (!empty($this->user_id)) {
			$typeQuery->user($this->user_id);
		}

		if ($this->getIssueMainType()) {
			$this->applyIssueMainTypeFilter($typeQuery);
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
				'url' => ['index', Html::getInputName($this, 'type_id') => $typeId, Url::PARAM_ISSUE_PARENT_TYPE => $parentTypeId],
				'active' => (int) $this->type_id === $typeId,
			];
		}
		if (!empty($typesItems)) {
			$typesItems[] = [
				'label' => Yii::t('common', 'All'),
				'url' => ['index', Url::PARAM_ISSUE_PARENT_TYPE => $parentTypeId],
				'active' => empty($this->type_id),
			];
		}

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
		$this->applyIssueMainTypeFilter($query);
		$ids = $query->column();
		return User::getSelectList($ids,
			false
		);
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
		$query->with('issue.tags.tagType');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		$dataProvider->sort->defaultOrder = [
			'start_at' => SORT_DESC,
		];

		$dataProvider->sort->attributes['customerLastname'] = [
			'asc' => ['CP.lastname' => SORT_ASC],
			'desc' => ['CP.lastname' => SORT_DESC],
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

		$this->applyIssueStageFilter($query);
		$this->applyCustomerNameFilter($query);
		$this->applyCustomerPhoneFilter($query);
		$this->applyIssueMainTypeFilter($query);

		$this->applyTagsFilter($query);
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

	protected function applyTagsFilter(SummonQuery $query): void {
		if (!empty($this->tagsIds)) {
			$query->joinWith('issue.tags');
			$query->distinct();
			$query->andWhere([IssueTagLink::tableName() . '.tag_id' => $this->tagsIds]);
		}
		if (!empty($this->excludedTagsIds)) {
			$query->joinWith('issue.tags');
			$query->distinct();
			$query->andWhere([
				'NOT IN', static::SUMMON_ALIAS . '.issue_id', IssueTagLink::find()
					->select('issue_id')
					->distinct()
					->andWhere(['tag_id' => $this->excludedTagsIds]),
			]);
		}
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

	public function applyIssueMainTypeFilter(ActiveQuery $query): void {
		if ($this->issueParentTypeId) {
			$query->joinWith([
				'issue' => function (IssueQuery $query) {
					$query->type($this->issueParentTypeId);
				},
			]);
		}
	}

	public function getIssueMainType(): ?IssueType {
		if ($this->issueParentTypeId) {
			return IssueType::get($this->issueParentTypeId);
		}
		return null;
	}

	public static function getTagsNames(): array {
		return IssueTag::getNamesGroupByType(true);
	}

	public function getIssueStagesNames(): array {
		return IssueStage::getStagesNames();
	}

	public function applyIssueStageFilter(QueryInterface $query): void {
		if (!empty($this->issueStageId)) {
			$query->joinWith(['issue']);
			$query->andWhere([Issue::tableName() . '.stage_id' => $this->issueStageId]);
		}
		if (!empty($this->excludedIssueStagesIds)) {
			$query->joinWith(['issue']);
			$query->andWhere(['NOT IN', Issue::tableName() . '.stage_id', $this->excludedIssueStagesIds]);
		}
	}
}

<?php

namespace common\models\issue\search;

use common\helpers\ArrayHelper;
use common\models\issue\Issue;
use common\models\issue\IssueType;
use common\models\issue\query\SummonDocLinkQuery;
use common\models\issue\query\SummonQuery;
use common\models\issue\Summon;
use common\models\issue\SummonDocLink;
use common\models\user\CustomerSearchInterface;
use common\models\user\User;
use common\validators\PhoneValidator;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\QueryInterface;

class SummonDocLinkSearch extends SummonDocLink implements
	CustomerSearchInterface,
	IssueParentTypeSearchable {

	public string $docName = '';
	public string $customerName = '';
	public string $customerPhone = '';
	public ?int $issueParentTypeId = null;
	public $issue_id;

	public $summonTypeId;

	public ?string $status = null;
	/**
	 * @var int|mixed|string|null
	 */
	public int $userId;

	public $summonContractorId;
	public $summonOwnerId;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[
				[
					'doc_type_id', 'summon_id', 'issue_id', 'summonTypeId', 'issueParentTypeId',
					'summonContractorId', 'summonOwnerId', 'done_user_id', 'confirmed_user_id',
				], 'integer',
			],
			['docName', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
			['customerName', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
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
		$query = SummonDocLink::find();

		$query->with([
			'doc',
			'summon',
			'summon.type',
			'summon.issue',
			'summon.issue.customer.userProfile',
		]);

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
		switch ($this->status) {
			case static::STATUS_TO_CONFIRM:
				$query->with('summon.owner.userProfile');
				$query->with('doneUser.userProfile');
				break;
			case static::STATUS_CONFIRMED:
				$query->with('doneUser.userProfile');
				$query->with('confirmedUser.userProfile');
				break;
		}

		$this->applyIssueIdFilter($query);
		$this->applyUserFilter($query);
		$this->applyDoneUserFilter($query);
		$this->applyConfirmedUserFilter($query);
		$this->applyIssueParentTypeFilter($query);
		$this->applySummonContractorFilter($query);
		$this->applySummonOwnerFilter($query);
		$this->applySummonTypeFilter($query);
		$this->applyCustomerNameFilter($query);
		$this->applyDocFilter($query);
		$this->applyStatusFilter($query);

		return $dataProvider;
	}

	public function getDocsNames(): array {
		//@todo add user filter
		return ArrayHelper::map(SummonDocLink::find()
			->select(['doc_type_id', 'name'])
			->joinWith('doc')
			->distinct()
			->asArray()
			->all(), 'doc_type_id', 'name');
	}

	public function getSummonTypesNames(): array {
		//@todo add user filter
		return ArrayHelper::map(SummonDocLink::find()
			->select(['type_id', 'name'])
			->joinWith('summon.type')
			->distinct()
			->asArray()
			->all(), 'type_id', 'name');
	}

	private function applySummonTypeFilter(ActiveQuery $query) {
		if (!empty($this->summonTypeId)) {
			$query->joinWith('summon');
			$query->andWhere([
				Summon::tableName() . '.type_id' => $this->summonTypeId,
			]);
		}
	}

	private function applyDocFilter(ActiveQuery $query) {
		if (!empty($this->doc_type_id)) {
			$query->andWhere([
				SummonDocLink::tableName() . '.doc_type_id' => $this->doc_type_id,
			]);
		}
	}

	public function getIssueParentType(): ?IssueType {
		if (empty($this->issueParentTypeId)) {
			return null;
		}
		return IssueType::get($this->issueParentTypeId);
	}

	public function applyCustomerNameFilter(QueryInterface $query): void {
		if (!empty($this->customerName)) {
			$query->joinWith([
				'summon.issue.customer.userProfile CP' => function (ActiveQuery $query) {
					$query->andWhere([
						'like',
						new Expression("CONCAT(CP.lastname,' ', CP.firstname)"),
						$this->customerName . '%', false,
					]);
					$query->orWhere([
						'like',
						new Expression("CONCAT(CP.firstname,' ', CP.lastname)"),
						$this->customerName . '%', false,
					]);
				},
			]);
		}
	}

	public function applyIssueParentTypeFilter(ActiveQuery $query): void {
		$parentType = $this->getIssueParentType();
		if ($parentType) {
			$childs = ArrayHelper::getColumn($parentType->childs, 'id');
			$query->joinWith('summon.issue');
			$query->andFilterWhere([Issue::tableName() . '.type_id' => $childs]);
		}
	}

	private function applyStatusFilter(SummonDocLinkQuery $query): void {
		if (!empty($this->status)) {
			$query->status($this->status);
		}
	}

	private function applyUserFilter(SummonDocLinkQuery $query) {
		if (!empty($this->userId)) {
			$query->joinWith([
				'summon' => function (SummonQuery $query) {
					$query->user($this->userId);
				},
			]);
		}
	}

	private function applySummonContractorFilter(SummonDocLinkQuery $query) {
		if (!empty($this->summonContractorId)) {
			$query->joinWith('summon');
			$query->andWhere([Summon::tableName() . '.contractor_id' => $this->summonContractorId]);
		}
	}

	private function applySummonOwnerFilter(SummonDocLinkQuery $query) {
		if (!empty($this->summonOwnerId)) {
			$query->joinWith('summon');
			$query->andWhere([Summon::tableName() . '.owner_id' => $this->summonOwnerId]);
		}
	}

	public function getSummonContractorsNames(): array {
		$query = SummonDocLink::find()
			->select('contractor_id')
			->distinct()
			->joinWith('summon');

		$this->applyStatusFilter($query);
		$this->applyUserFilter($query);
		$this->applySummonOwnerFilter($query);
		return User::getSelectList($query->column(), false);
	}

	public function getSummonOwnersNames(): array {
		$query = SummonDocLink::find()
			->select('owner_id')
			->distinct()
			->joinWith('summon');

		$this->applyStatusFilter($query);
		$this->applyUserFilter($query);
		return User::getSelectList($query->column(), false);
	}

	public function getDoneUsersNames(): array {
		$query = SummonDocLink::find()
			->select('done_user_id')
			->distinct();
		$this->applyStatusFilter($query);
		$this->applyUserFilter($query);
		return User::getSelectList($query->column(), false);
	}

	private function applyDoneUserFilter(SummonDocLinkQuery $query) {
		$query->andFilterWhere([
			SummonDocLink::tableName() . '.done_user_id' => $this->done_user_id,
		]);
	}

	private function applyConfirmedUserFilter(SummonDocLinkQuery $query) {
		$query->andFilterWhere([
			SummonDocLink::tableName() . '.confirmed_user_id' => $this->confirmed_user_id,
		]);
	}

	public function getConfirmedUsersNames(): array {
		$query = SummonDocLink::find()
			->select('confirmed_user_id')
			->distinct();
		$this->applyStatusFilter($query);
		$this->applyUserFilter($query);
		return User::getSelectList($query->column(), false);
	}

	private function applyIssueIdFilter(SummonDocLinkQuery $query) {
		if (!empty($this->issue_id)) {
			$query->joinWith('summon');
			$query->andWhere([
				Summon::tableName() . '.issue_id' => $this->issue_id,
			]);
		}
	}

}

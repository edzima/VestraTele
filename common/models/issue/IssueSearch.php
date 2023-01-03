<?php

namespace common\models\issue;

use common\helpers\ArrayHelper;
use common\models\AddressSearch;
use common\models\AgentSearchInterface;
use common\models\entityResponsible\EntityResponsible;
use common\models\issue\query\IssueQuery;
use common\models\issue\search\ArchivedIssueSearch;
use common\models\issue\search\IssueTypeSearch;
use common\models\query\PhonableQuery;
use common\models\SearchModel;
use common\models\user\CustomerSearchInterface;
use common\models\user\User;
use common\validators\PhoneValidator;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\QueryInterface;

/**
 * IssueSearch represents the model behind the search form of `common\models\issue\Issue`.
 */
abstract class IssueSearch extends Model
	implements AgentSearchInterface,
	ArchivedIssueSearch,
	CustomerSearchInterface,
	IssueTypeSearch,
	SearchModel {

	public $issue_id;
	public $stage_id;
	public $type_id;
	public $entity_responsible_id;
	public $type_additional_date_at;

	public $summonsStatusFilter;

	public ?int $parentTypeId = null;

	public string $created_at = '';
	public string $updated_at = '';
	public string $createdAtFrom = '';
	public string $createdAtTo = '';
	public string $signedAtFrom = '';
	public string $signedAtTo = '';
	public string $customerName = '';
	public string $customerPhone = '';
	public string $userName = '';

	public $excludedTypes = [];
	public $excludedStages = [];

	public $onlyWithTelemarketers;

	public $noteFilter;

	public const NOTE_ONLY_PINNED = 'only-pinned';

	public bool $withArchive = false;
	public bool $allowArchiveFilter = false;

	public $agent_id;
	public $lawyer_id;
	public $tele_id;

	public $tagsIds;

	public ?AddressSearch $addressSearch = null;

	public static function getSummonsStatusesNames(): array {
		return Summon::getStatusesNames();
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[
				[
					'issue_id', 'agent_id', 'stage_id', 'entity_responsible_id',
				], 'integer',
			],
			[['onlyWithTelemarketers'], 'boolean'],
			[['onlyWithTelemarketers'], 'default', 'value' => null],

			['noteFilter', 'string'],
			[['createdAtTo', 'createdAtFrom', 'signedAtFrom', 'signedAtTo'], 'date', 'format' => DATE_ATOM],
			['stage_id', 'in', 'range' => array_keys($this->getStagesNames())],
			[['type_id', 'excludedTypes'], 'in', 'range' => array_keys($this->getIssueTypesNames()), 'allowArray' => true],
			[['customerName', 'userName'], 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
			['tagsIds', 'in', 'range' => array_keys(static::getTagsNames()), 'allowArray' => true],
			[
				[
					'created_at', 'updated_at', 'type_additional_date_at',
				], 'safe',
			],
			['summonsStatusFilter', 'in', 'range' => array_keys(static::getSummonsStatusesNames())],
			['customerPhone', PhoneValidator::class],
			['excludedStages', 'in', 'range' => array_keys($this->getStagesNames()), 'allowArray' => true],
			['parentTypeId', 'in', 'range' => array_keys(static::getParentsTypesNames())],
			[
				'excludedStages', 'filter', 'filter' => function ($stages): array {
				$stages = (array) $stages;
				foreach ([$this->stage_id] as $id) {
					ArrayHelper::removeValue($stages, $id);
				}
				return $stages;
			},
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return array_merge([
			'agent_id' => IssueUser::getTypesNames()[IssueUser::TYPE_AGENT],
			'createdAtFrom' => Yii::t('common', 'Created at from'),
			'createdAtTo' => Yii::t('common', 'Created at to'),
			'excludedStages' => Yii::t('issue', 'Excluded stages'),
			'excludedTypes' => Yii::t('issue', 'Excluded types'),
			'issue_id' => Yii::t('issue', 'Issue'),
			'lawyer_id' => IssueUser::getTypesNames()[IssueUser::TYPE_LAWYER],
			'onlyWithTelemarketers' => Yii::t('issue', 'Only with Telemarketers'),
			'signedAtFrom' => Yii::t('issue', 'Signed At from'),
			'signedAtTo' => Yii::t('issue', 'Signed At to'),
			'tagsIds' => Yii::t('issue', 'Tags'),
			'tele_id' => IssueUser::getTypesNames()[IssueUser::TYPE_TELEMARKETER],
			'userName' => Yii::t('issue', 'Issue User'),
		], Issue::instance()->attributeLabels());
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios(): array {
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	abstract public function search(array $params): ActiveDataProvider;

	protected function issueQueryFilter(IssueQuery $query): void {
		$this->addressFilter($query);
		$this->archiveFilter($query);
		$this->applyAgentsFilters($query);
		$this->applyCustomerNameFilter($query);
		$this->applyCustomerPhoneFilter($query);
		$this->applyCreatedAtFilter($query);
		$this->applyNotesFilter($query);
		$this->excludedStagesFilter($query);
		$this->excludedTypesFilter($query);
		$this->applySignedAtFilter($query);
		$this->applyUserNameFilter($query);
		$this->applyTagsFilter($query);
		$this->applyOnlyWithTelemarketersFilter($query);
		$this->applyParentTypeFilter($query);
		$this->applySummonsStatusFilter($query);

		$query->andFilterWhere([
			Issue::tableName() . '.id' => $this->issue_id,
			Issue::tableName() . '.stage_id' => $this->stage_id,
			Issue::tableName() . '.type_id' => $this->type_id,
			Issue::tableName() . '.entity_responsible_id' => $this->entity_responsible_id,
			Issue::tableName() . '.type_additional_date_at' => $this->type_additional_date_at,
		]);
	}

	protected function addressFilter(IssueQuery $query): void {

		if ($this->addressSearch !== null && $this->addressSearch->validate()) {

			if ($this->addressSearch->isNotEmpty()) {
				if (empty($this->userName)) {
					$query->joinWith([
						'customer.addresses.address' => function (ActiveQuery $addressQuery) {
							$this->addressSearch->applySearch($addressQuery);
						},
					]);
				} else {
					$this->addError('userName', Yii::t('issue', 'Address filter not available with Issue User Name'));
				}
			}
		}
	}

	public function getParentType(): ?IssueType {
		if ($this->parentTypeId) {
			return IssueType::get($this->parentTypeId);
		}
		return null;
	}

	protected function issueWith(): array {
		return [
			'agent.userProfile',
			'customer.userProfile',
			'entityResponsible',
			'stage.types',
			'type',
			'issueNotes',
			'summons',
		];
	}

	protected function applyCreatedAtFilter(QueryInterface $query): void {

		if (!empty($this->createdAtFrom)) {
			$query->andFilterWhere([
				'>=', Issue::tableName() . '.created_at',
				date('Y-m-d 00:00:00', strtotime($this->createdAtFrom)),
			]);
		}

		if (!empty($this->createdAtTo)) {
			$query->andFilterWhere([
				'<=', Issue::tableName() . '.created_at',
				date('Y-m-d 23:59:59', strtotime($this->createdAtTo)),
			]);
		}
	}

	protected function applySignedAtFilter(QueryInterface $query): void {
		if (!empty($this->signedAtFrom)) {
			$query->andFilterWhere([
				'>=', Issue::tableName() . '.signing_at',
				date('Y-m-d 00:00:00', strtotime($this->signedAtFrom)),
			]);
		}

		if (!empty($this->signedAtTo)) {
			$query->andFilterWhere([
				'<=', Issue::tableName() . '.signing_at',
				date('Y-m-d 23:59:59', strtotime($this->signedAtTo)),
			]);
		}
	}

	protected function archiveFilter(IssueQuery $query): void {
		if (!$this->getWithArchive()) {
			$query->withoutArchives();
		}
	}

	public function applyAgentsFilters(QueryInterface $query): void {
		if (!empty($this->agent_id)) {
			$query->agents([$this->agent_id]);
		}
	}

	public function applyCustomerNameFilter(QueryInterface $query): void {
		if (!empty($this->customerName)) {
			$query->joinWith([
				'customer.userProfile CP' => function (ActiveQuery $query) {
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

	public function applyCustomerPhoneFilter(ActiveQuery $query): void {
		if (!empty($this->customerPhone)) {
			$query->joinWith([
				'customer.userProfile CP' => function (PhonableQuery $query) {
					$query->withPhoneNumber($this->customerPhone);
				},
			]);
		}
	}

	protected function applyOnlyWithTelemarketersFilter(IssueQuery $query): void {
		if ($this->onlyWithTelemarketers === null || $this->onlyWithTelemarketers === '') {
			return;
		}
		if ((bool) $this->onlyWithTelemarketers === true) {
			$query->joinWith('tele T', false);
			$query->andWhere('T.id IS NOT NULL');
		} else {
			$query->andWhere([
					'NOT IN', Issue::tableName() . '.id',
					IssueUser::find()
						->select('issue_id')
						->withType(IssueUser::TYPE_TELEMARKETER),
				]
			);
		}
	}

	protected function excludedStagesFilter(IssueQuery $query): void {
		$query->andFilterWhere(['NOT IN', Issue::tableName() . '.stage_id', $this->excludedStages]);
	}

	protected function excludedTypesFilter(IssueQuery $query): void {
		$query->andFilterWhere(['NOT IN', Issue::tableName() . '.type_id', $this->excludedTypes]);
	}

	public function applyUserNameFilter(ActiveQuery $query): void {
		if (!empty($this->userName)) {
			$query->joinWith([
				'users.user.userProfile UP' => function (ActiveQuery $query) {
					$query->andWhere([
						'like',
						new Expression("CONCAT(UP.lastname,' ', UP.firstname)"),
						$this->userName . '%', false,
					]);
				},
			]);
			$query->distinct();
		}
	}

	public function getWithArchive(): bool {
		return $this->withArchive;
	}

	public function getAgentsNames(): array {
		return User::getSelectList(
			IssueUser::userIds(IssueUser::TYPE_AGENT)
		);
	}

	public static function getLawyersNames(): array {
		return User::getSelectList(
			IssueUser::userIds(IssueUser::TYPE_LAWYER)
		);
	}

	public static function getTelemarketersNames(): array {
		return User::getSelectList(
			IssueUser::userIds(IssueUser::TYPE_TELEMARKETER)
		);
	}

	public static function getTagsNames(): array {
		return ArrayHelper::map(IssueTag::find()->asArray()->all(), 'id', 'name');
	}

	public static function getEntityNames(): array {
		return ArrayHelper::map(EntityResponsible::find()->asArray()->all(), 'id', 'name');
	}

	public function getStagesNames(): array {
		$stages = IssueStage::getStagesNames($this->getWithArchive());
		if ($this->getParentType() === null) {
			return $stages;
		}
		$parent = $this->getParentType();
		foreach ($stages as $id => $name) {
			if (!$parent->hasStage($id)) {
				unset($stages[$id]);
			}
		}
		return $stages;
	}

	public function getIssueTypesNames(): array {
		if ($this->getParentType()) {
			return ArrayHelper::map($this->getParentType()->childs, 'id', 'nameWithShort');
		}
		return IssueType::getTypesNamesWithShort();
	}

	public function applyIssueTypeFilter(QueryInterface $query): void {
		$query->andFilterWhere([Issue::tableName() . '.type_id' => $this->type_id]);
	}

	private function applyNotesFilter(IssueQuery $query) {
		switch ($this->noteFilter) {
			case static::NOTE_ONLY_PINNED:
				$query->joinWith('issueNotes');
				$query->andWhere([IssueNote::tableName() . '.is_pinned' => true]);
				break;
		}
	}

	private function applyTagsFilter(IssueQuery $query): void {
		if (!empty($this->tagsIds)) {
			$query->joinWith('tags');
			$query->distinct();
			$query->andWhere([IssueTag::tableName() . '.id' => $this->tagsIds]);
		}
	}

	public function excludeArchiveStage(): void {
		if ($this->getParentType() && !$this->getParentType()->hasStage(IssueStage::ARCHIVES_ID)) {
			return;
		}
		$this->excludedStages[] = IssueStage::ARCHIVES_ID;
	}

	public function hasExcludedArchiveStage(): bool {
		return in_array(IssueStage::ARCHIVES_ID, $this->excludedStages);
	}

	private function applyParentTypeFilter(IssueQuery $query): void {
		if (!empty($this->parentTypeId)) {
			$type = IssueType::get($this->parentTypeId);
			if ($type) {
				$childs = ArrayHelper::getColumn($type->childs, 'id');
				$query->andFilterWhere([Issue::tableName() . '.type_id' => $childs]);
			}
		}
	}

	public static function getParentsTypesNames(): array {
		return ArrayHelper::map(IssueType::getParents(), 'id', 'name');
	}

	private function applySummonsStatusFilter(IssueQuery $query): void {
		if (!empty($this->summonsStatusFilter)) {
			$query->joinWith('summons');
			$query->groupBy(Summon::tableName() . '.issue_id');
			$query->andWhere([
				Summon::tableName() . '.status' => $this->summonsStatusFilter,
			]);
		}
	}

}

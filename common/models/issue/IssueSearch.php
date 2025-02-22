<?php

namespace common\models\issue;

use backend\modules\issue\models\IssueStage;
use common\helpers\ArrayHelper;
use common\models\AddressSearch;
use common\models\AgentSearchInterface;
use common\models\entityResponsible\EntityResponsible;
use common\models\issue\query\IssueNoteQuery;
use common\models\issue\query\IssueQuery;
use common\models\issue\search\ArchivedIssueSearch;
use common\models\issue\search\IssueMainTypeSearchable;
use common\models\issue\search\IssueStageSearchable;
use common\models\issue\search\IssueTypeSearch;
use common\models\query\PhonableQuery;
use common\models\SearchModel;
use common\models\user\CustomerSearchInterface;
use common\models\user\User;
use common\validators\PhoneValidator;
use Yii;
use yii\base\InvalidConfigException;
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
	IssueMainTypeSearchable,
	IssueStageSearchable,
	IssueTypeSearch,
	SearchModel {

	public const SCENARIO_ARCHIVE_CUSTOMER = 'archive.customer';

	public $issue_id;
	public $stage_id;
	public $type_id;
	public $entity_responsible_id;
	public $type_additional_date_from_at;
	public $type_additional_date_to_at;

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
	public string $userType = '';

	public string $details = '';

	public $excludedTypes = [];
	public $excludedStages = [];

	public $excludedEntity = [];

	public $onlyWithTelemarketers;

	public $noteFilter;

	/**
	 * @var int|string|null
	 */
	public $userId;

	public const NOTE_ONLY_PINNED = 'only-pinned';

	public bool $withArchive = false;
	public bool $withArchiveDeep = false;

	public $agent_id;
	public $lawyer_id;
	public $tele_id;

	public $tagsIds;
	public $excludedTagsIds;

	public $note_stage_id;

	public $note_stage_change_from_at;
	public $note_stage_change_to_at;

	public $withoutTags;

	public const SUMMON_ALL_REALIZED = 'all-realized';
	public const SUMMON_SOME_ACTIVE = 'some-active';

	public const SUMMON_DOCS_SOME_TO_CONFIRM = 'docs.to-confirm';

	public ?AddressSearch $addressSearch = null;
	private array $stagesIdsForParentType = [];
	private ?array $existedIssueTypesIds = null;

	private $entityResponsibleNames = [];

	private array $stagesNames = [];
	private array $typesNames = [];

	protected static function getSummonsStatusFilters(): array {
		$filters = [];
		foreach (static::getSummonsStatusesNames() as $filtersNames) {
			foreach ($filtersNames as $filter => $name) {
				$filters[] = $filter;
			}
		}
		return $filters;
	}

	public static function getSummonsStatusesNames(): array {
		$statuses = [
				static::SUMMON_ALL_REALIZED => Yii::t('issue', 'All Realized'),
				static::SUMMON_SOME_ACTIVE => Yii::t('issue', 'Some Active'),
			] + Summon::getStatusesNames();
		return [
			Yii::t('issue', 'Status') => $statuses,
			Yii::t('issue', 'Summon Docs') => [
				static::SUMMON_DOCS_SOME_TO_CONFIRM => Yii::t('issue', 'To Confirm'),
			],
		];
	}

	public static function getIssueUserTypesNames(): array {
		$names = IssueUser::getTypesNames();
		$unsetTypes = [
			IssueUser::TYPE_AGENT,
			IssueUser::TYPE_LAWYER,
			IssueUser::TYPE_TELEMARKETER,
			IssueUser::TYPE_CUSTOMER,
		];
		foreach ($unsetTypes as $unsetType) {
			unset($names[$unsetType]);
		}
		return $names;
	}

	public function __construct($config = []) {
		if (!isset($config['addressSearch'])) {
			$config['addressSearch'] = new AddressSearch();
		}
		parent::__construct($config);
	}

	public function setScenario($value) {
		parent::setScenario($value);
		if ($value === static::SCENARIO_ARCHIVE_CUSTOMER) {
			$this->withArchiveDeep = true;
			$this->withArchive = true;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[
				[
					'issue_id', 'parentTypeId', 'agent_id', 'entity_responsible_id', 'note_stage_id',
				], 'integer',
			],
			[
				['customerName'], 'required', 'on' => static::SCENARIO_ARCHIVE_CUSTOMER, 'when' => function (): bool {
				return empty($this->issue_id);
			},
			],
			[
				['issue_id'], 'required', 'on' => static::SCENARIO_ARCHIVE_CUSTOMER, 'when' => function (): bool {
				return empty($this->customerName);
			},
			],
			[['details'], 'string'],
			[['onlyWithTelemarketers', 'withoutTags'], 'boolean'],
			[['onlyWithTelemarketers', 'withoutTags'], 'default', 'value' => null],
			['noteFilter', 'string'],
			[
				[
					'createdAtTo', 'createdAtFrom', 'signedAtFrom', 'signedAtTo',
					'type_additional_date_from_at', 'type_additional_date_to_at',
					'note_stage_change_from_at', 'note_stage_change_to_at',
				],
				'date', 'format' => DATE_ATOM,
			],
			['stage_id', 'in', 'range' => array_keys($this->getIssueStagesNames()), 'allowArray' => true],
			[['type_id', 'excludedTypes'], 'in', 'range' => array_keys($this->getIssueTypesNames()), 'allowArray' => true],
			[['excludedEntity'], 'in', 'range' => array_keys($this->getEntityResponsibleNames()), 'allowArray' => true],
			[['customerName', 'userName'], 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
			[['excludedTagsIds', 'tagsIds'], 'in', 'range' => array_keys(IssueTag::getModels()), 'allowArray' => true],
			[
				[
					'created_at', 'updated_at',
				], 'safe',
			],
			[['summonsStatusFilter', 'excludedStages'], 'safe'],
			['summonsStatusFilter', 'in', 'range' => static::getSummonsStatusFilters(), 'allowArray' => true],
			['customerPhone', PhoneValidator::class],
			['userType', 'in', 'range' => array_keys(static::getIssueUserTypesNames())],
			[
				'excludedStages', 'filter', 'filter' => function ($stages): array {
				$stages = array_map('intval', (array) $stages);
				foreach ([$this->stage_id] as $id) {
					ArrayHelper::removeValue($stages, (int) $id);
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
			'excludedTagsIds' => Yii::t('issue', 'Excluded tags'),
			'excludedEntity' => Yii::t('issue', 'Excluded entity'),
			'withoutTags' => Yii::t('issue', 'Without Tags'),
			'tele_id' => IssueUser::getTypesNames()[IssueUser::TYPE_TELEMARKETER],
			'userName' => Yii::t('issue', 'First name & surname'),
			'userType' => Yii::t('issue', 'Who'),
			'type_additional_date_from_at' => Yii::t('issue', 'Type additional Date at from'),
			'type_additional_date_to_at' => Yii::t('issue', 'Type additional Date at to'),
			'note_stage_id' => Yii::t('issue', 'Stage Change'),
			'note_stage_change_from_at' => Yii::t('issue', 'Stage Change from At'),
			'note_stage_change_to_at' => Yii::t('issue', 'Stage Change to At'),
			'showChart' => Yii::t('issue', 'Show Chart'),
		], Issue::instance()->attributeLabels());
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios(): array {
		return Model::scenarios();
	}

	public function load($data, $formName = null) {
		if (isset($data['parentTypeId'])
			&& $formName === null && !isset($data[$this->formName()]['parentTypeId'])) {
			$data[$this->formName()]['parentTypeId'] = $data['parentTypeId'];
		}
		return parent::load($data, $formName = null);
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
		$this->applyTypeDateAtFilter($query);
		$this->applyNotesFilter($query);
		$this->excludedStagesFilter($query);
		$this->excludedTypesFilter($query);
		$this->applySignedAtFilter($query);
		$this->applyUserNameFilter($query);
		$this->applyTagsFilter($query);
		$this->applyOnlyWithTelemarketersFilter($query);
		$this->applyIssueMainTypeFilter($query);
		$this->applySummonsStatusFilter($query);
		$this->applyIssueStageFilter($query);
		$this->noteStageFilter($query);
		$this->applyIssueDetailsFilter($query);
		$this->applyExcludedEntity($query);

		$query->andFilterWhere([
			Issue::tableName() . '.id' => $this->issue_id,
			Issue::tableName() . '.type_id' => $this->type_id,
			Issue::tableName() . '.entity_responsible_id' => $this->entity_responsible_id,
		]);
		$query->groupBy(Issue::tableName() . '.id');
	}

	public function applyIssueStageFilter(QueryInterface $query): void {
		if (!empty($this->stage_id)) {
			$query->andWhere([
				Issue::tableName() . '.stage_id' => $this->stage_id,
			]);
		}
	}

	public function getTotalCountWithArchive(): int {
		$self = clone $this;
		$self->withArchiveDeep = true;
		$self->withArchive = true;
		foreach (IssueStage::ARCHIVES_IDS as $id) {
			ArrayHelper::removeValue($self->excludedStages, $id);
		}
		return $self->search([])->totalCount;
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

	public function getIssueMainType(): ?IssueType {
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
			'summons.docsLink',
			'tags',
			'tags.tagType',
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

	protected function noteStageFilter(IssueQuery $query): void {
		if (!empty($this->note_stage_id)) {
			$query->joinWith([
				'issueNotes' => function (IssueNoteQuery $noteQuery) {
					$noteQuery->onlyStage($this->note_stage_id, false);
					if (!empty($this->note_stage_change_from_at)) {
						$noteQuery->andWhere([
								'>=',
								IssueNote::tableName() . '.publish_at',
								date('Y-m-d 00:00:00', strtotime($this->note_stage_change_from_at)),
							]
						);
					}
					if (!empty($this->note_stage_change_to_at)) {
						$noteQuery->andWhere([
							'<=',
							IssueNote::tableName() . '.publish_at',
							date('Y-m-d 23:59:59', strtotime($this->note_stage_change_to_at)),
						]);
					}
				},
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
		if (!$this->getWithArchiveDeep()) {
			$query->withoutArchiveDeep();
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
			if (empty($this->userType)) {
				$query->joinWith([
					'users.user.userProfile UP' => function (ActiveQuery $query) {
						$query->andWhere([
							'like',
							new Expression("CONCAT(UP.lastname,' ', UP.firstname)"),
							$this->userName . '%', false,
						]);
					},
				]);
			} else {
				$query->joinWith([
					'users IU' => function (ActiveQuery $userQuery) {
						$userQuery->andWhere(
							['IU.type' => $this->userType]
						);
						$userQuery->joinWith([
							'user.userProfile UP' => function (ActiveQuery $query) {
								$query->andWhere([
									'like',
									new Expression("CONCAT(UP.lastname,' ', UP.firstname)"),
									$this->userName . '%', false,
								]);
							},
						]);
					},
				]);
			}

			$query->distinct();
		}
	}

	public function getWithArchive(): bool {
		return $this->withArchive;
	}

	public function getWithArchiveDeep(): bool {
		return $this->withArchiveDeep;
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
		return IssueTag::getNamesGroupByType(true);
	}

	public function getEntityResponsibleNames(): array {
		if (empty($this->entityResponsibleNames)) {
			$this->entityResponsibleNames = ArrayHelper::map(EntityResponsible::find()->asArray()->all(), 'id', 'name');
		}
		return $this->entityResponsibleNames;
	}

	public function setEntityResponsibleNames(array $names): void {
		$this->entityResponsibleNames = $names;
	}

	public function setStagesNames(array $names): void {
		$this->stagesNames = $names;
	}

	public function getIssueStagesNames(): array {
		if (empty($this->stagesNames)) {
			$stages = IssueStage::getStagesNames($this->withArchive, $this->withArchiveDeep);
			if ($this->getIssueMainType() === null) {
				return $stages;
			}
			$this->stagesNames = array_filter($stages, function (int $stageId) {
				if ($this->getIssueMainType()->hasStage($stageId)) {
					return true;
				}
				foreach ($this->getIssueMainType()->childs as $type) {
					if ($type->hasStage($stageId)) {
						return true;
					}
				}
				return false;
			}, ARRAY_FILTER_USE_KEY);
		}
		return $this->stagesNames;
	}

	public function setTypesNames(array $names): void {
		$this->typesNames = $names;
	}

	public function getIssueTypesNames(): array {
		if (empty($this->typesNames)) {
			$this->typesNames = $this->getDefaultIssueTypesNames();
		}
		return $this->typesNames;
	}

	protected function getDefaultIssueTypesNames(): array {
		if ($this->getIssueMainType()) {
			$names = ArrayHelper::map($this->getIssueMainType()->childs, 'id', 'nameWithShort');
			if (empty($names)) {
				$names[$this->getIssueMainType()->id] = $this->getIssueMainType()->getNameWithShort();
			}
			return $this->filterExistedTypesNames($names);
		}
		return $this->filterExistedTypesNames(IssueType::getTypesNamesWithShort());
	}

	protected function filterExistedTypesNames(array $names): array {
		$ids = $this->getExistedIssueTypesIds();
		$filtered = [];
		foreach ($ids as $id) {
			if (isset($names[$id])) {
				$filtered[$id] = $names[$id];
			}
		}
		return $filtered;
	}

	protected function getExistedIssueTypesIds(): array {
		if ($this->existedIssueTypesIds === null) {
			$this->existedIssueTypesIds = Issue::find()
				->select('type_id')
				->distinct()
				->column();
		}
		return $this->existedIssueTypesIds;
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
		if ($this->withoutTags) {
			$query->joinWith('tags', false);
			$query->andWhere([IssueTag::tableName() . '.id' => null]);
			return;
		}
		if (!empty($this->tagsIds)) {
			$query->joinWith('tags');
			$query->distinct();
			$query->andWhere([IssueTagLink::tableName() . '.tag_id' => $this->tagsIds]);
		}
		if (!empty($this->excludedTagsIds)) {
			$query->joinWith('tags');
			$query->distinct();
			$query->andWhere([
				'NOT IN', Issue::tableName() . '.id', IssueTagLink::find()
					->select('issue_id')
					->distinct()
					->andWhere(['tag_id' => $this->excludedTagsIds]),
			]);
		}
	}

	public function excludeArchiveStage(): void {
		$this->excludeStage(IssueStage::ARCHIVES_ID);
	}

	public function excludeArchiveDeepStage(): void {
		$this->excludeStage(IssueStage::ARCHIVES_DEEP_ID);
	}

	public function excludeStage(int $stage_id): void {
		$this->excludedStages[] = $stage_id;
	}

	public function hasExcludedArchiveStage(): bool {
		return in_array(IssueStage::ARCHIVES_ID, $this->excludedStages)
			|| !$this->getWithArchiveDeep()
			|| !$this->getWithArchive();
	}

	/**
	 * @param IssueQuery $query
	 * @return void
	 */
	public function applyIssueMainTypeFilter(ActiveQuery $query): void {
		if ($this->parentTypeId) {
			$query->type($this->parentTypeId);
		}
	}

	public static function getMainTypesNames(): array {
		return ArrayHelper::map(IssueType::getMainTypes(), 'id', 'name');
	}

	private function applySummonsStatusFilter(IssueQuery $query): void {
		if (!empty($this->summonsStatusFilter)) {
			$query->joinWith('summons');
			$query->groupBy(Summon::tableName() . '.issue_id');
			$summonsStatuses = [];
			foreach ($this->summonsStatusFilter as $summonFilter) {
				switch ($summonFilter) {
					case static::SUMMON_DOCS_SOME_TO_CONFIRM:
						$query->joinWith('summons.docsLink');
						$query->andWhere(SummonDocLink::tableName() . '.summon_id IS NOT NULL');
						$query->andWhere([
							SummonDocLink::tableName() . '.confirmed_at' => null,
						]);
						break;
					case static::SUMMON_SOME_ACTIVE:
						$query->andWhere(['NOT IN', Summon::tableName() . '.status', [Summon::STATUS_REALIZED]]);
						break;
					case static::SUMMON_ALL_REALIZED:
						$query->andWhere([Summon::tableName() . '.status' => Summon::STATUS_REALIZED]);
						$query->andWhere([
							'NOT IN',
							Issue::tableName() . '.id',
							Summon::find()
								->andWhere(['<>', 'status', Summon::STATUS_REALIZED])
								->select('issue_id')
								->distinct(),
						]);
						break;
					default:
						$summonsStatuses[] = $summonFilter;
				}
			}
			if (!empty($summonsStatuses)) {
				$query->andWhere([Summon::tableName() . '.status' => $summonsStatuses]);
			}
		}
	}

	private function applyTypeDateAtFilter(IssueQuery $query) {
		if (!empty($this->type_additional_date_from_at)) {
			$query->andFilterWhere([
				'>=', Issue::tableName() . '.type_additional_date_at',
				date('Y-m-d 00:00:00', strtotime($this->type_additional_date_from_at)),
			]);
		}

		if (!empty($this->type_additional_date_to_at)) {
			$query->andFilterWhere([
				'<=', Issue::tableName() . '.type_additional_date_at',
				date('Y-m-d 23:59:59', strtotime($this->type_additional_date_to_at)),
			]);
		}
	}

	protected function applyIssueDetailsFilter(IssueQuery $query) {
		if (!empty($this->details)) {
			$query->andWhere(['like', Issue::tableName() . '.details', $this->details]);
		}
	}

	protected function onlyUserTypes(IssueQuery $query, bool $withChildren = true): void {
		if (empty($this->userId)) {
			throw new InvalidConfigException('userId cannot be empty');
		}
		$query->userTypes($this->userId, $withChildren);
	}

	protected function applyExcludedEntity(IssueQuery $query): void {
		if (!empty($this->excludedEntity)) {
			$query->andWhere(['NOT IN', Issue::tableName() . '.entity_responsible_id', $this->excludedEntity]);
		}
	}

}

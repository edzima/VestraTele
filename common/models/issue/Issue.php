<?php

namespace common\models\issue;

use common\behaviors\DateIDBehavior;
use common\helpers\ArrayHelper;
use common\models\entityResponsible\EntityResponsible;
use common\models\entityResponsible\EntityResponsibleQuery;
use common\models\issue\event\IssueUserEvent;
use common\models\issue\query\IssueCostQuery;
use common\models\issue\query\IssueNoteQuery;
use common\models\issue\query\IssuePayCalculationQuery;
use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\issue\query\IssueStageQuery;
use common\models\issue\query\IssueUserQuery;
use common\models\user\query\UserQuery;
use common\models\user\User;
use common\modules\court\models\Lawsuit;
use common\modules\file\models\AttachableModel;
use common\modules\file\models\File;
use common\modules\file\models\IssueFile;
use DateTime;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "issue".
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $details
 * @property int $stage_id
 * @property int $type_id
 * @property int $entity_responsible_id
 * @property string|null $archives_nr
 * @property string $signing_at
 * @property string|null $type_additional_date_at
 * @property string $stage_change_at
 * @property string|null $signature_act
 * @property string|null $stage_deadline_at
 * @property string|null $entity_agreement_details
 * @property string|null $entity_agreement_at
 * @property IssuePay[] $pays
 * @property EntityResponsible $entityResponsible
 * @property IssueStage $stage
 * @property IssueType $type
 * @property IssueNote[] $issueNotes
 * @property IssuePayCalculation[] $payCalculations
 * @property-read string $longId
 * @property-read User $agent
 * @property-read User $lawyer
 * @property-read User $customer
 * @property-read User|null $tele
 * @property-read Summon[] $summons
 * @property-read IssueTag[] $tags
 * @property-read IssueUser[] $users
 * @property-read IssueCost[] $costs
 * @property-read StageType $stageType
 * @property-read IssueClaim[] $claims
 * @property-read IssueRelation[] $issuesRelations
 * @property-read Issue[] $linkedIssues
 * @property-read IssueNote|null $newestNote
 * @property-read IssueFile[] $issueFiles
 */
class Issue extends ActiveRecord implements
	IssueInterface,
	AttachableModel {

	use IssueTrait;

	public function __toString(): string {
		return $this->longId;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%issue}}';
	}

	public function behaviors(): array {
		return [
			DateIDBehavior::class,
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('CURRENT_TIMESTAMP'),
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['created_at', 'updated_at', 'entity_agreement_at'], 'safe'],
			[['stage_id', 'type_id', 'entity_responsible_id',], 'required',],
			[['stage_id', 'type_id', 'entity_responsible_id'], 'integer'],
			[['details', 'signature_act', 'archives_nr', 'entity_agreement_details'], 'string'],
			[['details', 'signature_act', 'archives_nr', 'entity_agreement_details'], 'trim'],
			[['details', 'signature_act', 'archives_nr', 'entity_agreement_details', 'entity_agreement_at'], 'default', 'value' => null],
			[['entity_responsible_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntityResponsible::class, 'targetAttribute' => ['entity_responsible_id' => 'id']],
			[['stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueStage::class, 'targetAttribute' => ['stage_id' => 'id']],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueType::class, 'targetAttribute' => ['type_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'longId' => 'ID',
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'details' => Yii::t('common', 'Details'),
			'stage_id' => Yii::t('common', 'Stage'),
			'type_id' => Yii::t('common', 'Type'),
			'stage' => Yii::t('common', 'Stage'),
			'type' => Yii::t('common', 'Type'),
			'entity_responsible_id' => Yii::t('common', 'Entity responsible'),
			'entityResponsible' => Yii::t('common', 'Entity responsible'),
			'entity_agreement_at' => Yii::t('issue', 'Entity Agreement At'),
			'entity_agreement_details' => Yii::t('issue', 'Entity Agreement Details'),
			'signing_at' => Yii::t('common', 'Signing at'),
			'archives_nr' => Yii::t('common', 'Archives'),
			'type_additional_date_at' => $this->type
				? Yii::t('common', 'Date at ({type})', ['type' => $this->type->name])
				: Yii::t('common', 'Additional Date for Type'),
			'stage_change_at' => Yii::t('common', 'Stage date'),
			'signature_act' => Yii::t('issue', 'Signature act'),
			'customer' => IssueUser::getTypesNames()[IssueUser::TYPE_CUSTOMER],
			'tagsNames' => Yii::t('issue', 'Tags Names'),
			'stage_deadline_at' => Yii::t('issue', 'Stage Deadline At'),
			'stageName' => Yii::t('issue', 'Stage'),
		];
	}

	public function getTypeName(): string {
		return IssueType::getTypesNames()[$this->type_id];
	}

	public function getStageName(): string {
		return IssueStage::getStagesNames(true, true)[$this->stage_id];
	}

	public function getCustomer(): UserQuery {
		/** @todo change to customer after UserQuery join with assignment table */
		return $this->getUserType(IssueUser::TYPE_CUSTOMER, User::class);
	}

	public function getAgent(): UserQuery {
		return $this->getUserType(IssueUser::TYPE_AGENT, User::class);
	}

	public function getLawyer(): UserQuery {
		return $this->getUserType(IssueUser::TYPE_LAWYER, User::class);
	}

	public function getTele(): UserQuery {
		return $this->getUserType(IssueUser::TYPE_TELEMARKETER, User::class);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	protected function getUserType(string $type, string $userClass = User::class, callable $callable = null): UserQuery {
		return $this->hasOne($userClass, ['id' => 'user_id'])->via('users', function (IssueUserQuery $query) use ($type, $callable) {
			$query->alias($type);
			$query->withType($type);
			if ($callable !== null) {
				$callable($query, $type);
			}
		});
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getCosts(): IssueCostQuery {
		return $this->hasMany(IssueCost::class, ['issue_id' => 'id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getUsers(): IssueUserQuery {
		return $this->hasMany(IssueUser::class, ['issue_id' => 'id']);
	}

	public function getEntityResponsible(): EntityResponsibleQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(EntityResponsible::class, ['id' => 'entity_responsible_id']);
	}

	public function getStage(): IssueStageQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(IssueStage::class, ['id' => 'stage_id']);
	}

	public function getType(): ActiveQuery {
		return $this->hasOne(IssueType::class, ['id' => 'type_id']);
	}

	public function getStageType(): ActiveQuery {
		return $this->hasOne(StageType::class, ['type_id' => 'type_id', 'stage_id' => 'stage_id']);
	}

	public function getNewestNote(): IssueNoteQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(IssueNote::class, ['issue_id' => 'id'])
			->orderBy('publish_at DESC');
	}

	public function getIssueNotes(): IssueNoteQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(IssueNote::class, ['issue_id' => 'id'])
			->orderBy('publish_at DESC');
	}

	public function getSummons(): ActiveQuery {
		return $this->hasMany(Summon::class, ['issue_id' => 'id']);
	}

	public function getPayCalculations(): IssuePayCalculationQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(IssuePayCalculation::class, ['issue_id' => 'id']);
	}

	public function getPays(): IssuePayQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(IssuePay::class, ['calculation_id' => 'id'])
			->via('payCalculations');
	}

	public function getTags() {
		return $this->hasMany(IssueTag::class, ['id' => 'tag_id'])->viaTable(IssueTagLink::tableName(), ['issue_id' => 'id']);
	}

	public function isArchived(): bool {
		return in_array((int) $this->stage_id, IssueStage::ARCHIVES_IDS, true);
	}

	public function isDeepArchived(): bool {
		return (int) $this->stage_id === IssueStage::ARCHIVES_DEEP_ID;
	}

	public function getClaimsSum(string $attribute = 'trying_value'): ?float {
		if (empty($this->claims)) {
			return null;
		}
		$sum = 0;
		foreach ($this->claims as $claim) {
			$sum += $claim->{$attribute};
		}
		return $sum;
	}

	public function getClaims(): ActiveQuery {
		return $this->hasMany(IssueClaim::class, ['issue_id' => 'id']);
	}

	public function hasTele(): bool {
		return $this->tele !== null;
	}

	public function hasLawyer(): bool {
		return $this->lawyer !== null;
	}

	public function markAsUpdate(): void {
		$this->touch('updated_at');
	}

	public function getTagsNames(): ?string {
		if (empty($this->tags)) {
			return null;
		}
		return implode(', ', ArrayHelper::getColumn($this->tags, 'name'));
	}

	public function linkIssue(int $issueId): void {
		$relation = new IssueRelation();
		$relation->issue_id_1 = $this->id;
		$relation->issue_id_2 = $issueId;
		$this->link('issuesRelations', $relation);
	}

	public function linkUser(int $userId, string $type): void {
		$issueUser = $this->getIssueUser($type);

		if ($issueUser === null) {
			$issueUser = new IssueUser(['user_id' => $userId, 'type' => $type]);
			$this->link('users', $issueUser);
			$this->afterLinkUserCreate($issueUser);
		} elseif ($issueUser->user_id !== $userId) {
			$issueUser->user_id = $userId;
			$issueUser->save();
			$this->afterLinkUserUpdate($issueUser);
		}
	}

	public function afterLinkUserCreate(IssueUser $issueUser): void {
		$this->trigger(IssueUserEvent::EVENT_AFTER_LINK_USER_CREATE, new IssueUserEvent(['model' => $issueUser]));
	}

	public function afterLinkUserUpdate(IssueUser $issueUser): void {
		$this->trigger(IssueUserEvent::EVENT_AFTER_LINK_USER_UPDATE, new IssueUserEvent(['model' => $issueUser]));
	}

	public function unlinkUser(string $type): void {
		$user = $this->getIssueUser($type);
		if ($user !== null) {
			$this->unlink('users', $user, true);
			$this->afterUnlinkUser($user);
		}
	}

	public function afterUnlinkUser(IssueUser $issueUser): void {
		$this->trigger(IssueUserEvent::EVENT_UNLINK_USER, new IssueUserEvent(['model' => $issueUser]));
	}

	private function getIssueUser(string $type): ?IssueUser {
		$users = $this->users;
		foreach ($users as $issueUser) {
			if ($issueUser->type === $type) {
				return $issueUser;
			}
		}
		return null;
	}

	/**
	 * @inheritdoc
	 * @return IssueQuery the active query used by this AR class.
	 */
	public static function find(): IssueQuery {
		return new IssueQuery(static::class);
	}

	public function isForUser(int $id): bool {
		return $this->getUsers()
			->andWhere(['user_id' => $id])
			->exists();
	}

	public function isForAgents(array $ids): bool {
		return $this->getUsers()
			->withType(IssueUser::TYPE_AGENT)
			->andWhere(['user_id' => $ids])
			->exists();
	}

	public function getIssueModel(): Issue {
		return $this;
	}

	public static function getIssueIdAttribute(): string {
		return 'id';
	}

	public function getIssueRelationId(int $issueId): ?int {
		return array_search($issueId, $this->getLinkedIssuesIds());
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getLinkedIssues(): IssueQuery {
		return $this->hasMany(static::class, ['id' => 'linkedIssuesIds']);
	}

	/**
	 * @return int[] Linked Issues IDs indexed by Relation ID.
	 */
	public function getLinkedIssuesIds(): array {
		$relations = $this->issuesRelations;
		$ids = [];
		foreach ($relations as $relation) {
			if ($relation->issue_id_1 === $this->id) {
				$ids[$relation->id] = $relation->issue_id_2;
			} else {
				$ids[$relation->id] = $relation->issue_id_1;
			}
		}
		return $ids;
	}

	public function getIssuesRelations(): ActiveQuery {
		return $this->hasMany(IssueRelation::class, ['issue_id_1' => 'id'])
			->onCondition('1=1) OR (issue_id_2 = :id', [':id' => $this->id]);
	}

	public function hasDelayedStage(): ?bool {
		if (empty($this->stage_change_at)) {
			return null;
		}
		if (!empty($this->stage_deadline_at)) {
			return strtotime($this->stage_deadline_at) < time();
		}
		$days = $this->getIssueStage()->days_reminder;
		if (empty($days)) {
			return null;
		}
		$date = new DateTime($this->stage_change_at);
		$daysDiff = $date->diff(new DateTime())->days;
		return $daysDiff >= $days;
	}

	public function generateStageDeadlineAt(): void {
		$days = $this->getDaysReminder();
		if ($days > 0) {
			$date = new DateTime($this->stage_change_at);
			$date->modify("+ $days days");
			$this->stage_deadline_at = $date->format('Y-m-d H:i:s');
		} else {
			$this->stage_deadline_at = null;
		}
	}

	private function getDaysReminder(): ?int {
		return $this->getIssueStageType() ? $this->getIssueStageType()->days_reminder : null;
	}

	public function getIssueStageType(): ?IssueStageType {
		$stage = IssueStage::get($this->stage_id);
		$stageType = $stage->stageTypes[$this->type_id] ?? null;
		if ($stageType === null) {
			$type = IssueType::get($this->type_id);
			if ($type->parent_id) {
				$stageType = $stage->stageTypes[$type->parent_id] ?? null;
			}
		}
		return $stageType;
	}

	public function hasUser(int $id, string $type = null): bool {
		foreach ($this->users as $issueUser) {
			if ($issueUser->user_id === $id) {
				return $type === null || $issueUser->type === $type;
			}
		}
		return false;
	}

	public function summonsHasUser(int $userId): ?bool {
		$summons = $this->summons;
		if (empty($summons)) {
			return null;
		}
		foreach ($summons as $summon) {
			if ($summon->isForUser($userId)) {
				return true;
			}
		}
		return false;
	}

	public function linkFile(File $file): void {
		$issueFile = $this->issueFiles[$file->id] ?? new IssueFile();
		$issueFile->issue_id = $this->id;
		$issueFile->file_id = $file->id;
		$issueFile->save();
	}

	public function getDirParts(): array {
		return [
			'issue',
			$this->id,
		];
	}

	public function getIssueFiles(): ActiveQuery {
		return $this
			->hasMany(IssueFile::class, ['issue_id' => 'id'])
			->joinWith('file')
			->indexBy('file_id');
	}

	public function getFilesByType(int $fileTypeId): array {
		$types = [];
		foreach ($this->issueFiles as $issueFile) {
			if ($issueFile->file->file_type_id === $fileTypeId) {
				$types[$issueFile->file_id] = $issueFile->file;
			}
		}
		return $types;
	}

	public function getShipmentsPocztaPolska(): ActiveQuery {
		return $this->hasMany(IssueShipmentPocztaPolska::class, ['issue_id' => 'id']);
	}

	public function getLawsuits(): ActiveQuery {
		return $this->hasMany(Lawsuit::class, ['id' => 'lawsuit_id'])
			->viaTable(Lawsuit::VIA_TABLE_ISSUE, ['issue_id' => 'id'])
			->orderBy([Lawsuit::tableName() . '.due_at' => SORT_ASC]);
	}

	public function getUserRoles(int $userId): array {
		$roles = [];
		foreach ($this->users as $issueUser) {
			if ($issueUser->user_id === $userId) {
				$roles[] = $issueUser->type;
			}
		}
		return $roles;
	}

}

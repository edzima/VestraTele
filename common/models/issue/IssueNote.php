<?php

namespace common\models\issue;

use common\models\issue\query\IssueNoteQuery;
use common\models\issue\query\IssueQuery;
use common\models\user\query\UserQuery;
use common\models\user\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "issue_note".
 *
 * @property int $id
 * @property int $issue_id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property int $created_at
 * @property int $updated_at
 * @property int $publish_at
 * @property string $type
 * @property int $is_pinned
 * @property int $is_template
 * @property int|null $updater_id
 * @property string|null $show_on_linked_issues
 *
 * @property Issue $issue
 * @property User $user
 * @property User|null $updater
 *
 * @property-read string|null $typeName
 */
class IssueNote extends ActiveRecord implements IssueInterface {

	use IssueTrait;

	public bool $updateIssueAfterSave = true;
	public bool $updateIssueAfterDelete = true;

	public const TYPE_SMS = 'sms';
	public const TYPE_SETTLEMENT = 'settlement';
	public const TYPE_SETTLEMENT_PROVISION_CONTROL = 'settlement.provisionControl';

	public const TYPE_SUMMON = 'summon';
	public const TYPE_STAGE_CHANGE = 'stage.change';
	public const TYPE_USER_FRONT = 'user.front';
	public const TYPE_SELF = 'self';

	public ?string $typeName = null;

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%issue_note}}';
	}

	public function behaviors(): array {
		return [
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('CURRENT_TIMESTAMP'),
			],
		];
	}

	public function afterSave($insert, $changedAttributes) {
		if ($this->updateIssueAfterSave) {
			$this->issue->markAsUpdate();
		}
		parent::afterSave($insert, $changedAttributes);
	}

	public function afterDelete() {
		if ($this->updateIssueAfterDelete) {
			$this->issue->markAsUpdate();
		}
		parent::afterDelete();
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'issue_id' => Yii::t('common', 'Issue'),
			'user_id' => Yii::t('common', 'User'),
			'title' => Yii::t('common', 'Title'),
			'description' => Yii::t('common', 'Description'),
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'publish_at' => Yii::t('common', 'Publish at'),
			'is_pinned' => Yii::t('common', 'Is Pinned'),
			'is_template' => Yii::t('common', 'Is Template'),
			'type' => Yii::t('common', 'Type'),
			'typeFullName' => Yii::t('common', 'Type'),
			'updater_id' => Yii::t('common', 'Updater'),
			'updater' => Yii::t('common', 'Updater'),
			'show_on_linked_issues' => Yii::t('common', 'Show on Linked Issues'),
		];
	}



	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getIssue(): IssueQuery {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getUser(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getUpdater(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'updater_id']);
	}

	public function isPinned(): bool {
		return (bool) $this->is_pinned;
	}

	public function isForSettlement(): bool {
		return $this->isType(static::TYPE_SETTLEMENT);
	}

	public function isForSettlementProvisionControl(): bool {
		return $this->isType(static::TYPE_SETTLEMENT_PROVISION_CONTROL);
	}

	public function isForStageChange(): bool {
		return $this->isType(static::TYPE_STAGE_CHANGE);
	}

	public function isUserFrontend(): bool {
		return $this->isType(static::TYPE_USER_FRONT);
	}

	public function isSelf(): bool {
		return $this->isType(static::TYPE_SELF);
	}

	public function isForSummon(): bool {
		return $this->isType(static::TYPE_SUMMON);
	}

	public function isSms(): bool {
		return $this->isType(static::TYPE_SMS);
	}

	public function isType(string $type): bool {
		return StringHelper::startsWith($this->type, $type);
	}

	public function getEntityId(): string {
		if (empty($this->type)) {
			return $this->issue_id;
		}
		return StringHelper::explode($this->type, ':')[1];
	}

	public function getTypeKind(): ?string {
		return StringHelper::explode($this->type, ':')[0] ?? null;
	}

	public function getTypeKindName(): ?string {
		$typeKind = $this->getTypeKind();
		if ($typeKind) {
			return static::getTypesNames()[$typeKind];
		}
		return null;
	}

	public function getTypeFullName(): ?string {
		$typeKind = $this->getTypeKindName();
		if (!$typeKind) {
			return null;
		}
		return $typeKind . ' - ' . $this->getEntityId();
	}

	public static function generateType(string $type, string $id): string {
		return "$type:$id";
	}

	public static function genereateSmsType(string $phone, string $id): string {
		return static::generateType(static::generateType(static::TYPE_SMS, $id), $phone);
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_SETTLEMENT => Yii::t('settlement', 'Settlement'),
			static::TYPE_SUMMON => Yii::t('common', 'Summon'),
			static::TYPE_SMS => Yii::t('common', 'SMS'),
			static::TYPE_STAGE_CHANGE => Yii::t('common', 'Stage Change'),
			static::TYPE_SELF => Yii::t('common', 'Self'),
			static::TYPE_USER_FRONT => Yii::t('common', 'User Frontend'),
			static::TYPE_SETTLEMENT_PROVISION_CONTROL => Yii::t('settlement', 'Provision Control'),
		];
	}

	public static function pinnedNotesFilter(array $notes): array {
		return array_filter($notes, static function (IssueNote $note) {
			return $note->isPinned();
		});
	}

	/**
	 * @inheritdoc
	 * @return IssueNoteQuery the active query used by this AR class.
	 */
	public static function find(): IssueNoteQuery {
		return new IssueNoteQuery(static::class);
	}

	public function setShowOnLinkedIssues(array $ids): void {
		$this->show_on_linked_issues = implode('|', $ids);
	}

	public function hideOnLinkedIssues(): void {
		$this->show_on_linked_issues = null;
	}

	public function showOnAllLinkedIssues(): void {
		$this->show_on_linked_issues = '';
	}

	public function getShowOnLinkedIssuesIds(): ?array {
		if ($this->show_on_linked_issues === null) {
			return null;
		}
		return explode('|', $this->show_on_linked_issues);
	}

}

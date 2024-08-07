<?php

namespace common\models\issue;

use common\helpers\ArrayHelper;
use common\models\entityResponsible\EntityResponsible;
use common\models\issue\query\SummonQuery;
use common\models\user\User;
use common\modules\reminder\models\Reminder;
use common\modules\reminder\models\ReminderQuery;
use edzima\teryt\models\Simc;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "summon".
 *
 * @property int $id
 * @property int $status
 * @property int $type_id
 * @property string|null $title
 * @property int $created_at
 * @property int $updated_at
 * @property string $start_at
 * @property string|null $realize_at
 * @property string|null $realized_at
 * @property string|null $deadline_at
 * @property string|null $reminder_at
 * @property int $issue_id
 * @property int $owner_id
 * @property int $contractor_id
 * @property int|null $updater_id
 * @property int|null $entity_id
 * @property int|null $city_id
 *
 * @property-read string $statusName
 * @property-read string $typeName
 * @property-read string $entityWithCity
 *
 * @property-read SummonType $type
 * @property-read Issue $issue
 * @property-read User $contractor
 * @property-read User $owner
 * @property-read User|null $updater
 * @property-read Simc|null $city
 * @property-read EntityResponsible|null $entityResponsible
 * @property-read SummonDoc[] $docs
 * @property-read SummonDocLink[] $docsLink
 * @property-read Reminder[] $reminders
 *
 */
class Summon extends ActiveRecord implements IssueInterface {

	use IssueTrait;

	public const STATUS_NEW = 1;
	public const STATUS_IN_PROGRESS = 2;
	public const STATUS_WITHOUT_RECOGNITION = 3;
	public const STATUS_TO_CONFIRM = 4;
	public const STATUS_REALIZED = 5;
	public const STATUS_SUSPENDED = 15;

	public const STATUS_UNREALIZED_CLIENT = 6;
	public const STATUS_UNREALIZED_AGENT = 10;
	public const STATUS_UNREALIZED_COMPANY = 8;

	public const STATUSES_UNREALIZED = [
		self::STATUS_UNREALIZED_CLIENT,
		self::STATUS_UNREALIZED_COMPANY,
		self::STATUS_UNREALIZED_AGENT,
	];

	public static function notActiveStatuses(): array {
		$statuses = static::STATUSES_UNREALIZED;
		$statuses[] = static::STATUS_REALIZED;
		return $statuses;
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors(): array {
		return [
			TimestampBehavior::class,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%summon}}';
	}

//	/**
//	 * {@inheritdoc}
//	 */
//	public function rules(): array {
//		return [
//			[['status', 'issue_id', 'owner_id', 'type_id'], 'required'],
//			[['status', 'issue_id', 'owner_id', 'contractor_id'], 'integer'],
//			[['title'], 'string', 'max' => 255],
//			[['created_at', 'updated_at', 'realized_at', 'start_at', 'deadline_at'], 'safe'],
//			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
//			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SummonType::class, 'targetAttribute' => ['type_id' => 'id']],
//			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
//			[['entity_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntityResponsible::class, 'targetAttribute' => ['entity_id' => 'id']],
//			[['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Simc::class, 'targetAttribute' => ['city_id' => 'id']],
//			[['contractor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['contractor_id' => 'id']],
//			[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner_id' => 'id']],
//			[['updater_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updater_id' => 'id']],
//		];
//	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'type' => Yii::t('common', 'Type'),
			'type_id' => Yii::t('common', 'Type'),
			'typeName' => Yii::t('common', 'Type'),
			'status' => Yii::t('common', 'Status'),
			'statusName' => Yii::t('common', 'Status'),
			'title' => Yii::t('common', 'Title'),
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'start_at' => Yii::t('common', 'Start at'),
			'realize_at' => Yii::t('common', 'Realize at'),
			'realized_at' => Yii::t('common', 'Realized at'),
			'issue_id' => Yii::t('common', 'Issue'),
			'owner_id' => Yii::t('common', 'Owner'),
			'issue' => 'Sprawa',
			'owner' => Yii::t('common', 'Owner'),
			'updater' => Yii::t('common', 'Updater'),
			'contractor_id' => Yii::t('common', 'Contractor'),
			'contractor' => Yii::t('common', 'Contractor'),
			'city_id' => Yii::t('address', 'City'),
			'city' => Yii::t('address', 'City'),
			'entity_id' => Yii::t('common', 'Entity responsible'),
			'entity' => Yii::t('common', 'Entity responsible'),
			'entityWithCity' => Yii::t('common', 'Entity responsible'),
			'deadline_at' => Yii::t('common', 'Deadline at'),
			'doc_types_ids' => Yii::t('common', 'Doc Types'),
			'docsNames' => Yii::t('common', 'Doc Types'),
			'titleWithDocs' => Yii::t('issue', 'Title with Docs'),
			'docsCountSummary' => Yii::t('issue', 'Docs Count Summary'),
		];
	}

	public function getTitleWithDocs(): ?string {
		if (empty($this->title)) {
			return $this->getDocsNames();
		}
		if (empty($this->docs)) {
			return $this->title;
		}
		return $this->title . ' - ' . $this->getDocsNames();
	}

	public function getDocsNames(): ?string {
		$docs = $this->docs;
		if (empty($docs)) {
			return null;
		}
		return implode(', ', ArrayHelper::getColumn($docs, 'name'));
	}

	public function getType(): ActiveQuery {
		return $this->hasOne(SummonType::class, ['id' => 'type_id']);
	}

	public function getEntityWithCity(): ?string {
		if ($this->entityResponsible === null) {
			return null;
		}
		if ($this->city === null) {
			return $this->entityResponsible->name;
		}
		return $this->entityResponsible->name .
			' - '
			. $this->city->name;
	}

	public function getTypeName(): string {
		return $this->type->name;
	}

	public function getStatusName(): string {
		return static::getStatusesNames()[$this->status];
	}

	public static function getStatusesNames(): array {
		return [
			static::STATUS_NEW => Yii::t('common', 'New'),
			static::STATUS_IN_PROGRESS => Yii::t('common', 'In progress'),
			static::STATUS_WITHOUT_RECOGNITION => Yii::t('common', 'Without Recognition'),
			static::STATUS_TO_CONFIRM => Yii::t('common', 'To Confirm'),
			static::STATUS_REALIZED => Yii::t('common', 'Realized'),
			static::STATUS_SUSPENDED => Yii::t('common', 'Suspended'),
			static::STATUS_UNREALIZED_CLIENT => Yii::t('common', 'Unrealized - Client'),
			static::STATUS_UNREALIZED_COMPANY => Yii::t('common', 'Unrealized - Company'),
			static::STATUS_UNREALIZED_AGENT => Yii::t('common', 'Unrealized - Agent'),
		];
	}

	public function isRealized(): bool {
		return (int) $this->status === static::STATUS_REALIZED;
	}

	public function isUnrealized(): bool {
		return in_array((int) $this->status, static::STATUSES_UNREALIZED, true);
	}

	/**
	 * Gets query for [[Issue]].
	 *
	 * @return ActiveQuery
	 */
	public function getIssue() {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	/**
	 * Gets query for [[EntityResponsible]].
	 *
	 * @return ActiveQuery
	 */
	public function getEntityResponsible() {
		return $this->hasOne(EntityResponsible::class, ['id' => 'entity_id']);
	}

	public function getCity() {
		return $this->hasOne(Simc::class, ['id' => 'city_id']);
	}

	public function getDocs() {
		return $this->hasMany(SummonDoc::class, ['id' => 'doc_type_id'])->via('docsLink');
	}

	public function getDocsLink() {
		return $this->hasMany(SummonDocLink::class, ['summon_id' => 'id']);
	}

	public function getReminders(): ReminderQuery {
		return $this->hasMany(Reminder::class, ['id' => 'reminder_id'])->via('remindersLink');
	}

	public function getRemindersLink() {
		return $this->hasMany(SummonReminder::class, ['summon_id' => 'id']);
	}

	/**
	 * Gets query for [[Contractor]].
	 *
	 * @return ActiveQuery
	 */
	public function getContractor() {
		return $this->hasOne(User::class, ['id' => 'contractor_id']);
	}

	/**
	 * Gets query for [[Owner]].
	 *
	 * @return ActiveQuery
	 */
	public function getOwner() {
		return $this->hasOne(User::class, ['id' => 'owner_id']);
	}

	public function getUpdater() {
		return $this->hasOne(User::class, ['id' => 'updater_id']);
	}

	public function isForUser(int $id): bool {
		return $this->isContractor($id) || $this->isOwner($id);
	}

	public function isOwner(int $id): bool {
		return $this->owner_id === $id;
	}

	public function isContractor(int $id): bool {
		return $this->contractor_id === $id;
	}

	public function getName(): string {
		return Yii::t('common', 'Summon {type}', ['type' => $this->getTypeName()]);
	}

	public static function find(): SummonQuery {
		return new SummonQuery(static::class);
	}

	public function getDocsCountSummary(): ?string {
		return SummonDocLink::countSummary($this->docsLink);
	}

	public function isDelayed(): bool {
		return !$this->isRealized()
			&& (strtotime($this->deadline_at) < time());
	}
}

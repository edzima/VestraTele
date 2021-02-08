<?php

namespace common\models\issue;

use common\models\entityResponsible\EntityResponsible;
use common\models\user\User;
use edzima\teryt\models\Simc;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "summon".
 *
 * @property int $id
 * @property int $status
 * @property int $type
 * @property string $title
 * @property int $created_at
 * @property int $updated_at
 * @property string $start_at
 * @property string|null $realize_at
 * @property string|null $realized_at
 * @property string|null $deadline_at
 * @property int $issue_id
 * @property int $owner_id
 * @property int $contractor_id
 * @property int $entity_id
 * @property int $city_id
 *
 * @property-read string $statusName
 * @property-read string $typeName
 * @property-read string $entityWithCity
 *
 * @property-read Issue $issue
 * @property-read User $contractor
 * @property-read User $owner
 * @property-read Simc $city
 * @property-read EntityResponsible $entityResponsible
 * @property int $term [smallint]
 */
class Summon extends ActiveRecord implements IssueInterface {

	use IssueTrait;

	public const STATUS_NEW = 1;
	public const STATUS_IN_PROGRESS = 2;
	public const STATUS_WITHOUT_RECOGNITION = 3;
	public const STATUS_TO_CONFIRM = 4;
	public const STATUS_REALIZED = 5;
	public const STATUS_UNREALIZED = 6;

	public const TYPE_APPEAL = 10;
	public const TYPE_INCOMPLETE_DOCUMENTATION = 15;
	public const TYPE_PHONE = 20;
	public const TYPE_ANTIVINDICATION = 30;
	public const TYPE_URGENCY = 40;
	public const TYPE_RESIGNATION = 50;



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

	public function beforeSave($insert) {
		if (empty($this->realize_at)) {
			// set hours
			$this->realize_at = $this->start_at;
		}
		if (empty($this->realized_at) && $this->isRealized()) {
			$this->realized_at = date('Y-m-d H:i:s');
		}
		return parent::beforeSave($insert);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['status', 'title', 'issue_id', 'owner_id', 'contractor_id', 'entity_id', 'city_id', 'type'], 'required'],
			[['status', 'issue_id', 'owner_id', 'contractor_id'], 'integer'],
			[['title'], 'string', 'max' => 255],
			[['created_at', 'updated_at', 'realized_at', 'start_at'], 'safe'],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			[['entity_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntityResponsible::class, 'targetAttribute' => ['entity_id' => 'id']],
			[['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Simc::class, 'targetAttribute' => ['city_id' => 'id']],
			[['contractor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['contractor_id' => 'id']],
			[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'type' => Yii::t('common', 'Type'),
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
			'contractor_id' => Yii::t('common', 'Contractor'),
			'contractor' => Yii::t('common', 'Contractor'),
			'city_id' => Yii::t('address', 'City'),
			'city' => Yii::t('address', 'City'),
			'entity_id' => Yii::t('common', 'Entity responsible'),
			'entity' => Yii::t('common', 'Entity responsible'),
			'entityWithCity' => Yii::t('common', 'Entity responsible'),
			'deadline_at' => Yii::t('common', 'Deadline at'),
		];
	}

	public function getEntityWithCity(): string {
		return $this->entityResponsible->name . ' - ' . $this->city->name;
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	public function getStatusName(): string {
		return static::getStatusesNames()[$this->status];
	}






	//@todo add I18n
	public static function getStatusesNames(): array {
		return [
			static::STATUS_NEW => 'Nowe',
			static::STATUS_IN_PROGRESS => 'W trakcie realizacji',
			static::STATUS_WITHOUT_RECOGNITION => 'Bez rozpoznania',
			static::STATUS_TO_CONFIRM => 'Do potwierdzenia',
			static::STATUS_REALIZED => 'Zrealizowane',
			static::STATUS_UNREALIZED => 'Niezrealizowane',
		];
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_APPEAL => Yii::t('common', 'Appeal'),
			static::TYPE_INCOMPLETE_DOCUMENTATION => Yii::t('common', 'Incomplete documentation'),
			static::TYPE_PHONE => Yii::t('common', 'Phonable summon'),
			static::TYPE_ANTIVINDICATION => Yii::t('common', 'Antyvindication'),
			static::TYPE_RESIGNATION => Yii::t('common', 'Resignation'),
			static::TYPE_URGENCY => Yii::t('common', 'Urgency'),
		];
	}

	public function isRealized(): bool {
		return (int) $this->status === static::STATUS_REALIZED;
	}

	/**
	 * Gets query for [[Issue]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssue() {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	/**
	 * Gets query for [[EntityResponsible]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getEntityResponsible() {
		return $this->hasOne(EntityResponsible::class, ['id' => 'entity_id']);
	}

	public function getCity() {
		return $this->hasOne(Simc::class, ['id' => 'city_id']);
	}

	/**
	 * Gets query for [[Contractor]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getContractor() {
		return $this->hasOne(User::class, ['id' => 'contractor_id']);
	}

	/**
	 * Gets query for [[Owner]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getOwner() {
		return $this->hasOne(User::class, ['id' => 'owner_id']);
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
}

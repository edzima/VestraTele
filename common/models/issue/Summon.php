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
 * @property int|null $term
 * @property string $title
 * @property int $created_at
 * @property int $updated_at
 * @property string $start_at
 * @property string|null $realize_at
 * @property string|null $realized_at
 * @property int $issue_id
 * @property int $owner_id
 * @property int $contractor_id
 * @property int $entity_id
 * @property int $city_id
 *
 * @property-read string $statusName
 * @property-read string $typeName
 * @property-read string $termName
 * @property-read string $entityWithCity
 *
 * @property-read Issue $issue
 * @property-read User $contractor
 * @property-read User $owner
 * @property-read Simc $city
 * @property-read EntityResponsible $entityResponsible
 */
class Summon extends ActiveRecord implements IssueInterface {

	use IssueTrait;

	public const STATUS_NEW = 1;
	public const STATUS_IN_PROGRESS = 2;
	public const STATUS_WITHOUT_RECOGNITION = 3;
	public const STATUS_TO_CONFIRM = 4;
	public const STATUS_REALIZED = 5;
	public const STATUS_UNREALIZED = 6;

	public const TYPE_DOCUMENTS = 10;
	public const TYPE_INCOMPLETE_DOCUMENTATION = 15;
	public const TYPE_PHONE = 20;
	public const TYPE_ANTIVINDICATION = 30;

	public const TERM_EMPTY = null;
	public const TERM_ONE_DAY = 1;
	public const TERM_TREE_DAYS = 3;
	public const TERM_FIVE_DAYS = 5;
	public const TERM_ONE_WEEK = 7;
	public const TERM_TWO_WEEKS = 14;
	public const TERM_THREE_WEEKS = 21;
	public const TERM_ONE_MONTH = 30;

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
			['term', 'in', 'range' => array_keys(static::getTermsNames())],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			[['entity_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntityResponsible::class, 'targetAttribute' => ['entity_id' => 'id']],
			[['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Simc::class, 'targetAttribute' => ['city_id' => 'id']],
			[['contractor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['contractor_id' => 'id']],
			[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner_id' => 'id']],
		];
	}

	/**
	 * @todo add I18n
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'type' => 'Typ',
			'typeName' => 'Typ',
			'status' => 'Status',
			'statusName' => 'Status',
			'term' => 'Termin',
			'termName' => 'Termin',
			'title' => 'Tytuł',
			'created_at' => 'Data utworzenia',
			'updated_at' => 'Data aktualizacji',
			'start_at' => 'Data wezwania',
			'realize_at' => 'Wykonaj dnia',
			'realized_at' => 'Data realizacji',
			'issue_id' => 'Sprawa',
			'owner_id' => 'Zlecający',
			'issue' => 'Sprawa',
			'owner' => 'Zlecający',
			'contractor_id' => 'Realizujący',
			'contractor' => 'Realizujący',
			'city_id' => Yii::t('address', 'City'),
			'city' => Yii::t('address', 'City'),
			'entity_id' => Yii::t('common', 'Entity responsible'),
			'entity' => Yii::t('common', 'Entity responsible'),
			'entityWithCity' => Yii::t('common', 'Entity responsible'),

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

	public function getTermName(): string {
		return static::getTermsNames()[$this->term];
	}

	public function getDeadline(): ?string {
		if ($this->hasTerm()) {
			return date('Y-m-d', strtotime($this->start_at . " + {$this->term} days"));
		}
		return null;
	}

	public function hasTerm(): bool {
		return $this->term !== null;
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

	//@todo add I18n
	public static function getTermsNames(): array {
		return [
			static::TERM_ONE_DAY => '1 dzień',
			static::TERM_TREE_DAYS => '3 dni',
			static::TERM_FIVE_DAYS => '5 dni',
			static::TERM_ONE_WEEK => 'Tydzień',
			static::TERM_TWO_WEEKS => '2 tygodnie',
			static::TERM_THREE_WEEKS => '3 tygodnie ',
			static::TERM_ONE_MONTH => 'Miesiąc',
			static::TERM_EMPTY => 'Bez terminu',
		];
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_DOCUMENTS =>Yii::t('common','Documents'),
			static::TYPE_INCOMPLETE_DOCUMENTATION => Yii::t('common','Incomplete documentation'),
			static::TYPE_PHONE =>  Yii::t('common','Phonable'),
			static::TYPE_ANTIVINDICATION =>  Yii::t('common','Antyvindication'),
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
		return $this->contractor_id === $id || $this->owner_id === $id;
	}

	public function getName(): string {
		return Yii::t('common','Summon {type}',['type' => $this->getTypeName()]);
	}
}

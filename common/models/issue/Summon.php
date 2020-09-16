<?php

namespace common\models\issue;

use common\models\user\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "summon".
 *
 * @property int $id
 * @property int $status
 * @property string $title
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $realized_at
 * @property int $issue_id
 * @property int $owner_id
 * @property int $contractor_id
 *
 * @property-read Issue $issue
 * @property-read User $contractor
 * @property-read User $owner
 * @property-read string $statusName
 */
class Summon extends ActiveRecord {

	public const STATUS_NEW = 1;
	public const STATUS_IN_PROGRESS = 2;
	public const STATUS_WITHOUT_RECOGNITION = 3;
	public const STATUS_TO_CONFIRM = 4;
	public const STATUS_REALIZED = 5;
	public const STATUS_UNREALIZED = 6;

	public const TERM_ONE_WEEK = 7;
	public const TERM_TWO_WEEKS = 14;
	public const TERM_THREE_WEEKS = 21;
	public const TERM_ONE_MONTH = 30;

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			TimestampBehavior::class,
			[
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'owner_id',
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%summon}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['status', 'title', 'issue_id', 'owner_id', 'contractor_id'], 'required'],
			[['status', 'issue_id', 'owner_id', 'contractor_id'], 'integer'],
			[['title'], 'string', 'max' => 255],
			[
				['created_at', 'realized_at'], 'default',
				'value' => static function () {
					return date(DATE_ATOM);
				},
			],
			['created_at', 'filter', 'filter' => 'strtotime'],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
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
			'status' => 'Status',
			'title' => 'Tyuł',
			'created_at' => 'Data stworzenia',
			'updated_at' => 'Data aktualizacji',
			'realized_at' => 'Data realizacji',
			'issue_id' => 'Sprawa',
			'owner_id' => 'Właściciel',
			'contractor_id' => 'Wykonawca',
		];
	}

	public function getStatusName(): string {
		return static::getStatusesNames()[$this->status];
	}

	public static function getStatusesNames(): array {
		return [
			static::TERM_ONE_WEEK => 'Nowe',
			static::STATUS_IN_PROGRESS => 'W trakcie realizacji',
			static::STATUS_WITHOUT_RECOGNITION => 'Bez rozpoznania',
			static::STATUS_TO_CONFIRM => 'Do potwierdzenia',
			static::STATUS_REALIZED => 'Zrealizowane',
			static::STATUS_UNREALIZED => 'Niezrealizowane',
		];
	}

	public static function getTermsNames(): array {
		return [
			static::TERM_ONE_WEEK => 'Tydzień',
			static::TERM_TWO_WEEKS => '2 tygodnie',
			static::TERM_THREE_WEEKS => '3 tygodnie ',
			static::TERM_ONE_MONTH => 'Miesiąc',
		];
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
}

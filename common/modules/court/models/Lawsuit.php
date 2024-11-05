<?php

namespace common\modules\court\models;

use common\helpers\ArrayHelper;
use common\models\issue\Issue;
use common\models\user\User;
use common\modules\court\models\query\LawsuitQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "lawsuit".
 *
 * @property int $id
 * @property int $court_id
 * @property string|null $signature_act
 * @property string|null $room
 * @property string|null $details
 * @property string|null $due_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $location
 * @property int $creator_id
 * @property int|null $online
 * @property int|null $presence_of_the_claimant
 * @property int $is_appeal
 * @property string|null $url
 *
 * @property Court $court
 * @property User $creator
 * @property Issue[] $issues
 */
class Lawsuit extends ActiveRecord {

	public const LOCATION_STATIONARY = 'S';
	public const LOCATION_ONLINE = 'O';

	public const PRESENCE_OF_THE_CLAIMANT_REQUIRED = 1;
	public const PRESENCE_OF_THE_CLAIMANT_NOT_REQUIRED = 0;
	public const VIA_TABLE_ISSUE = '{{%lawsuit_issue}}';

	public function behaviors(): array {
		return array_merge(parent::behaviors(), [
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('NOW()'),
			],
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lawsuit}}';
	}

	public function getName(): string {
		return Yii::t('court', 'Lawsuit {n,plural,=1{for Issue:{issue}} other{for Issues: {issue}}}', [
			'n' => count($this->issues),
			'issue' => implode(', ', ArrayHelper::getColumn($this->issues, 'issueName')),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['court_id', 'creator_id', 'presence_of_the_claimant'], 'required'],
			[['court_id', 'creator_id', 'presence_of_the_claimant'], 'integer'],
			[['is_appeal', 'boolean']],
			[['due_at', 'created_at', 'updated_at'], 'safe'],
			[['location',], 'string', 'max' => 2],
			[['signature_act', 'room', 'details', 'url'], 'string', 'max' => 255],
			[['signature_act', 'room', 'details', 'location', 'url'], 'default', 'value' => null],
			['location', 'in', 'range' => array_keys(static::getLocationNames())],
			[['court_id'], 'exist', 'skipOnError' => true, 'targetClass' => Court::class, 'targetAttribute' => ['court_id' => 'id']],
			[['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('court', 'ID'),
			'issue_id' => Yii::t('court', 'Issue ID'),
			'court_id' => Yii::t('court', 'Court'),
			'courtName' => Yii::t('court', 'Court'),
			'signature_act' => Yii::t('court', 'Signature Act'),
			'details' => Yii::t('court', 'Details'),
			'room' => Yii::t('court', 'Room'),
			'due_at' => Yii::t('court', 'Due At'),
			'created_at' => Yii::t('court', 'Created At'),
			'updated_at' => Yii::t('court', 'Updated At'),
			'creator_id' => Yii::t('court', 'Creator ID'),
			'creator' => Yii::t('court', 'Creator'),
			'location' => Yii::t('court', 'Location'),
			'locationName' => Yii::t('court', 'Location'),
			'presence_of_the_claimant' => Yii::t('court', 'Presence of the Claimant'),
			'presenceOfTheClaimantName' => Yii::t('court', 'Presence of the Claimant'),
			'is_appeal' => Yii::t('court', 'Is Appeal'),
			'url' => Yii::t('court', 'URL'),
		];
	}

	public function getCourtName(): string {
		return $this->court->name;
	}

	public function getLocationName(): ?string {
		return static::getLocationNames()[$this->location] ?? null;
	}

	public function getPresenceOfTheClaimantName(): ?string {
		return static::getPresenceOfTheClaimantNames()[$this->presence_of_the_claimant] ?? null;
	}

	/**
	 * Gets query for [[Court]].
	 *
	 * @return ActiveQuery
	 */
	public function getCourt() {
		return $this->hasOne(Court::class, ['id' => 'court_id']);
	}

	/**
	 * Gets query for [[Creator]].
	 *
	 * @return ActiveQuery
	 */
	public function getCreator() {
		return $this->hasOne(User::class, ['id' => 'creator_id']);
	}

	/**
	 * Gets query for [[Issue]].
	 *
	 * @return ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::class, ['id' => 'issue_id'])
			->viaTable(static::VIA_TABLE_ISSUE, ['lawsuit_id' => 'id']);
	}

	/**
	 * @return int[]
	 */
	public function getIssuesIds(): array {
		return ArrayHelper::getColumn($this->issues, 'id');
	}

	public function unlinkIssue(int $issueId): bool {
		return static::getDb()
				->createCommand()
				->delete(static::VIA_TABLE_ISSUE, [
						'lawsuit_id' => $this->id,
						'issue_id' => $issueId,
					]
				)->execute() > 0;
	}

	public function linkIssues(array $ids): int {
		$rows = [];
		foreach ($ids as $id) {
			$rows[] = [
				'lawsuit_id' => $this->id,
				'issue_id' => $id,
			];
		}
		if (empty($rows)) {
			return 0;
		}
		return static::getDb()
			->createCommand()
			->batchInsert(static::VIA_TABLE_ISSUE, [
				'lawsuit_id',
				'issue_id',
			],
				$rows
			)->execute();
	}

	public static function getLocationNames(): array {
		return [
			static::LOCATION_STATIONARY => Yii::t('court', 'Stationary'),
			static::LOCATION_ONLINE => Yii::t('court', 'Online'),
		];
	}

	public static function getPresenceOfTheClaimantNames(): array {
		return [
			static::PRESENCE_OF_THE_CLAIMANT_REQUIRED => Yii::t('court', 'Required'),
			static::PRESENCE_OF_THE_CLAIMANT_NOT_REQUIRED => Yii::t('court', 'Not required'),
		];
	}

	public function isAfterDueAt(): ?bool {
		if (empty($this->due_at)) {
			return null;
		}
		return strtotime($this->due_at) < strtotime('now');
	}

	public function hasIssueUser(int $userId): bool {
		foreach ($this->issues as $issue) {
			if ($issue->isForUser($userId)) {
				return true;
			}
		}
		return false;
	}

	public static function find(): LawsuitQuery {
		return new LawsuitQuery(static::class);
	}
}

<?php

namespace common\modules\court\models;

use common\helpers\ArrayHelper;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\issue\query\IssueNoteQuery;
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
 * @property string $signature_act
 * @property string|null $details
 * @property string $created_at
 * @property string $updated_at
 * @property int $creator_id
 * @property int $is_appeal
 * @property string|null $result
 * @property string|null $spi_last_sync_at
 * @property string|null $spi_last_update_at
 * @property int $spi_confirmed_user
 *
 * @property Court $court
 * @property User $creator
 * @property Issue[] $issues
 * @property LawsuitSession[] $sessions
 */
class Lawsuit extends ActiveRecord {

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
			[['court_id', 'creator_id', 'signature_act'], 'required'],
			[['court_id', 'creator_id', 'presence_of_the_claimant'], 'integer'],
			[['is_appeal', 'boolean']],
			[['due_at', 'created_at', 'updated_at'], 'safe'],
			[['signature_act', 'details', 'result'], 'string', 'max' => 255],
			[['signature_act', 'details'], 'default', 'value' => null],
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
			'created_at' => Yii::t('court', 'Created At'),
			'updated_at' => Yii::t('court', 'Updated At'),
			'creator_id' => Yii::t('court', 'Creator ID'),
			'creator' => Yii::t('court', 'Creator'),
			'is_appeal' => Yii::t('court', 'Is Appeal'),
			'result' => Yii::t('court', 'Result'),
			'spi_last_update_at' => Yii::t('court', 'SPI Last Update At'),
			'spi_last_sync_at' => Yii::t('court', 'SPI Last Sync At'),
			'spi_is_confirm_update' => Yii::t('court', 'SPI has confirm Update'),
		];
	}

	public function getCourtName(): string {
		return $this->court->name;
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

	public function getSpiConfirmedUser() {
		return $this->hasOne(User::class, ['id' => 'spi_confirmed_user']);
	}

	public function getSessions(): ActiveQuery {
		return $this->hasMany(LawsuitSession::class, ['lawsuit_id' => 'id']);
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

	public function getNotes(): IssueNoteQuery {
		/**
		 * @var IssueNoteQuery $query
		 */
		$query = $this->hasMany(
			IssueNote::class,
			['issue_id' => 'id'])
			->via('issues');
		$query->onlyType(
			IssueNote::TYPE_LAWSUIT,
			$this->id
		);
		return $query;
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

	public function hasAllSessionsAfterDueAt(): ?bool {
		$sessions = $this->sessions;
		if (empty($sessions)) {
			return null;
		}
		$after = array_filter($sessions, function (LawsuitSession $session): bool {
			return $session->isAfterDueAt();
		});
		return count($sessions) === count($after);
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

	public function getNextSession(): ?LawsuitSession {
		$sessions = $this->sessions;
		ArrayHelper::multisort($sessions, 'date_at', SORT_ASC);
		foreach ($sessions as $session) {
			if (!$session->isAfterDueAt()) {
				return $session;
			}
		}
		return null;
	}

	public function hasIssue(int $issueId): bool {
		$ids = $this->getIssuesIds();
		return in_array($issueId, $ids);
	}
}

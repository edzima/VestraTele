<?php

namespace common\models\issue;

use common\models\issue\query\IssueNoteQuery;
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
 * @property string $description
 * @property int $created_at
 * @property int $updated_at
 * @property int $type
 *
 * @property Issue $issue
 * @property User $user
 */
class IssueNote extends ActiveRecord {

	public const TYPE_PAY = 'pay';
	public const TYPE_SUMMON = 'summon';

	public ?string $typeName = null;

	public static function generateType(string $type, int $id): string {
		return "$type:$id";
	}

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
		$this->issue->markAsUpdate();
		parent::afterSave($insert, $changedAttributes);
	}

	public function afterDelete() {
		$this->issue->markAsUpdate();
		parent::afterDelete();
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['issue_id', 'user_id', 'title', 'description'], 'required'],
			[['issue_id', 'user_id'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['title', 'type'], 'string', 'max' => 255],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'issue_id' => 'Issue ID',
			'user_id' => 'User ID',
			'title' => 'Tytuł',
			'description' => 'Szczegóły',
			'created_at' => 'Dodano',
			'updated_at' => 'Zaktualizowano',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssue() {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function isType(string $type): bool {
		return StringHelper::startsWith($this->type, $type);
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_PAY => Yii::t('common', 'Pay'),
			static::TYPE_SUMMON => Yii::t('common', 'Summon'),
		];
	}

	public function isPayType(): bool {
		return (int) $this->type === static::TYPE_PAY;
	}

	/**
	 * @inheritdoc
	 * @return IssueNoteQuery the active query used by this AR class.
	 */
	public static function find(): IssueNoteQuery {
		return new IssueNoteQuery(static::class);
	}
}

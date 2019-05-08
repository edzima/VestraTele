<?php

namespace common\models\issue;

use common\models\User;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

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
 *
 * @property Issue $issue
 * @property User $user
 */
class IssueNote extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'issue_note';
	}

	public function behaviors() {
		return [
			[
				'class' => TimestampBehavior::className(),
				'value' => new Expression('CURRENT_TIMESTAMP'),
			],
		];
	}

	public function afterSave($insert, $changedAttributes) {
		$this->issue->touch('updated_at');
		parent::afterSave($insert, $changedAttributes);
	}

	public function afterDelete() {
		$this->issue->touch('updated_at');
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
			[['title', 'description'], 'string', 'max' => 255],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::className(), 'targetAttribute' => ['issue_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
		return $this->hasOne(Issue::className(), ['id' => 'issue_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @inheritdoc
	 * @return IssueNoteQuery the active query used by this AR class.
	 */
	public static function find() {
		return new IssueNoteQuery(get_called_class());
	}
}

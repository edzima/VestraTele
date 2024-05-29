<?php

namespace common\modules\file\models;

use common\models\issue\Issue;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_file".
 *
 * @property int $file_id
 * @property int $issue_id
 * @property string|null $details
 *
 * @property File $file
 * @property Issue $issue
 */
class IssueFile extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%issue_file}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['file_id', 'issue_id'], 'required'],
			[['file_id', 'issue_id'], 'integer'],
			[['details'], 'string', 'max' => 255],
			[['file_id', 'issue_id'], 'unique', 'targetAttribute' => ['file_id', 'issue_id']],
			[['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['file_id' => 'id']],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'file_id' => Yii::t('file', 'File ID'),
			'issue_id' => Yii::t('file', 'Issue ID'),
			'details' => Yii::t('file', 'Details'),
		];
	}

	/**
	 * Gets query for [[File]].
	 *
	 * @return ActiveQuery
	 */
	public function getFile() {
		return $this->hasOne(File::class, ['id' => 'file_id']);
	}

	/**
	 * Gets query for [[Issue]].
	 *
	 * @return ActiveQuery
	 */
	public function getIssue() {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

}

<?php

namespace common\models\issue;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_tag_link".
 *
 * @property int $tag_id
 * @property int $issue_id
 *
 * @property Issue $issue
 * @property IssueTag $tag
 */
class IssueTagLink extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%issue_tag_link}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['tag_id', 'issue_id'], 'required'],
			[['tag_id', 'issue_id'], 'integer'],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			[['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueTag::class, 'targetAttribute' => ['tag_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'tag_id' => Yii::t('issue', 'Tag ID'),
			'issue_id' => Yii::t('issue', 'Issue ID'),
		];
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
	 * Gets query for [[Tag]].
	 *
	 * @return ActiveQuery
	 */
	public function getTag() {
		return $this->hasOne(IssueTag::class, ['id' => 'tag_id']);
	}
}

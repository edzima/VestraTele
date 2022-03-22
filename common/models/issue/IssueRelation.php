<?php

namespace common\models\issue;

use common\models\issue\query\IssueQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_relation".
 *
 * @property int $id
 * @property int $issue_id_1
 * @property int $issue_id_2
 * @property int $created_at
 *
 * @property Issue $issue
 * @property Issue $issue2
 */
class IssueRelation extends ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public function behaviors(): array {
		return [
			'time' => [
				'class' => TimestampBehavior::class,
				'updatedAtAttribute' => false,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%issue_relation}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['issue_id_1', 'issue_id_2'], 'required'],
			[['issue_id_1', 'issue_id_2', 'created_at'], 'integer'],
			[['issue_id_1', 'issue_id_2'], 'unique', 'targetAttribute' => ['issue_id_1', 'issue_id_2']],
			[['issue_id_1'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id_1' => 'id']],
			[['issue_id_2'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id_2' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('issue', 'ID'),
			'issue_id_1' => Yii::t('issue', 'Issue'),
			'issue_id_2' => Yii::t('issue', 'Issue'),
			'created_at' => Yii::t('issue', 'Created At'),
		];
	}

	public function getIssue(): IssueQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(Issue::class, ['id' => 'issue_id_1']);
	}

	public function getIssue2(): IssueQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(Issue::class, ['id' => 'issue_id_2']);
	}
}

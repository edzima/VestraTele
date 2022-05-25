<?php

namespace common\models\issue;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%issue_tag}}".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int|null $is_active
 *
 * @property IssueTagLink[] $issueTagLinks
 */
class IssueTag extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%issue_tag}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			['name', 'unique'],
			[['is_active'], 'integer'],
			[['name', 'description'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('issue', 'ID'),
			'name' => Yii::t('issue', 'Name'),
			'description' => Yii::t('issue', 'Description'),
			'is_active' => Yii::t('issue', 'Is Active'),
		];
	}

	/**
	 * Gets query for [[IssueTagLinks]].
	 *
	 * @return ActiveQuery
	 */
	public function getIssueTagLinks(): ActiveQuery {
		return $this->hasMany(IssueTagLink::class, ['tag_id' => 'id']);
	}
}

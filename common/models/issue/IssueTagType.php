<?php

namespace common\models\issue;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_tag_type".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $background
 * @property string|null $color
 * @property string|null $css_class
 * @property string|null $view_issue_position
 *
 * @property IssueTag[] $issueTags
 */
class IssueTagType extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%issue_tag_type}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'background', 'color', 'css_class', 'view_issue_position'], 'string', 'max' => 255],
			[['name'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => Yii::t('issue', 'Name'),
			'background' => Yii::t('common', 'Background'),
			'color' => Yii::t('common', 'Color'),
			'css_class' => Yii::t('common', 'Css Class'),
			'view_issue_position' => Yii::t('common', 'View Issue Position'),
		];
	}

	/**
	 * Gets query for [[IssueTags]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssueTags() {
		return $this->hasMany(IssueTag::class, ['type' => 'id']);
	}
}

<?php

namespace common\models\issue;

use common\helpers\ArrayHelper;
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

	public const VIEW_ISSUE_POSITION_CUSTOMER = 'customer';
	public const VIEW_ISSUE_POSITION_BEFORE_DETAILS = 'beforeDetails';
	public const VIEW_ISSUE_POSITION_AFTER_DETAILS = 'afterDetails';

	/**
	 * @param IssueTag[] $tags
	 * @param string $position
	 * @return IssueTag[]
	 */
	public static function viewIssuePositionFilter(array $tags, string $position): array {
		$models = [];
		foreach ($tags as $tag) {
			if ($tag->tagType && $tag->tagType->view_issue_position === $position) {
				$models[$tag->id] = $tag;
			}
		}
		return $models;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%issue_tag_type}}';
	}

	public function getViewIssuePositionName(): ?string {
		return static::getViewIssuePositionNames()[$this->view_issue_position];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'background', 'color', 'css_class', 'view_issue_position'], 'string', 'max' => 255],
			[['color', 'css_class', 'view_issue_position'], 'default', 'value' => null],
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
			'viewIssuePositionName' => Yii::t('common', 'View Issue Position'),
			'tagsCount' => Yii::t('common', 'Tags Count'),
			'issuesCount' => Yii::t('issue', 'Issues Count'),
		];
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(IssueTagType::find()->all(), 'id', 'name');
	}

	/**
	 * Gets query for [[IssueTags]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssueTags() {
		return $this->hasMany(IssueTag::class, ['type' => 'id']);
	}

	public function getTagsCount(): int {
		return count($this->issueTags);
	}

	public function getIssuesCount(): int {
		$count = 0;
		foreach ($this->issueTags as $issueTag) {
			$count += $issueTag->getIssuesCount();
		}
		return $count;
	}

	public static function getViewIssuePositionNames(): array {
		return [
			static::VIEW_ISSUE_POSITION_CUSTOMER => Yii::t('issue', 'Issue View Position - Customer'),
			static::VIEW_ISSUE_POSITION_AFTER_DETAILS => Yii::t('issue', 'Issue View Position - After Details'),
			static::VIEW_ISSUE_POSITION_BEFORE_DETAILS => Yii::t('issue', 'Issue View Position - Before Details'),

		];
	}
}

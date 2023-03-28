<?php

namespace common\models\issue;

use common\helpers\ArrayHelper;
use Yii;
use yii\db\ActiveQuery;
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
 * @property string|null $issues_grid_position
 * @property string|null $link_issues_grid_position
 * @property int|null $sort_order
 *
 * @property IssueTag[] $issueTags
 */
class IssueTagType extends ActiveRecord {

	public const VIEW_ISSUE_POSITION_CUSTOMER = 'customer';

	public const VIEW_ISSUE_BEFORE_CUSTOMERS = 'beforeCustomer';
	public const VIEW_ISSUE_POSITION_BEFORE_DETAILS = 'beforeDetails';
	public const VIEW_ISSUE_POSITION_AFTER_DETAILS = 'afterDetails';

	public const ISSUES_GRID_POSITION_COLUMN_CUSTOMER_BOTTOM = 'column-Customer_bottom';
	public const ISSUES_GRID_POSITION_COLUMN_ISSUE_BOTTOM = 'column-Issue_bottom';

	public const ISSUES_GRID_POSITION_COLUMN_TYPE_BOTTOM = 'column-Type_bottom';

	public const ISSUES_GRID_POSITION_COLUMN_STAGE_BOTTOM = 'column-Stage_bottom';

	public const LINK_ISSUES_GRID_POSITION_COLUMN_CUSTOMER_BOTTOM = 'column-Customer_bottom';
	public const LINK_ISSUES_GRID_POSITION_COLUMN_ISSUE_BOTTOM = 'column-Issue_bottom';

	public static function positionFilter(array $tags, ?string $position): array {
		switch ($position) {
			case self::ISSUES_GRID_POSITION_COLUMN_ISSUE_BOTTOM:
			case self::ISSUES_GRID_POSITION_COLUMN_CUSTOMER_BOTTOM:
			case self::ISSUES_GRID_POSITION_COLUMN_STAGE_BOTTOM:
			case self::ISSUES_GRID_POSITION_COLUMN_TYPE_BOTTOM:
				return static::issuesGridPositionFilter($tags, $position);
			case self::VIEW_ISSUE_POSITION_BEFORE_DETAILS:
			case self::VIEW_ISSUE_POSITION_AFTER_DETAILS:
			case self::VIEW_ISSUE_BEFORE_CUSTOMERS:
				return static::viewIssuePositionFilter($tags, $position);
		}
		return $tags;
	}

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
	 * @param IssueTag[] $tags
	 * @param string $position
	 * @return IssueTag[]
	 */
	public static function issuesGridPositionFilter(array $tags, string $position): array {
		$models = [];
		foreach ($tags as $tag) {
			if ($tag->tagType && $tag->tagType->issues_grid_position === $position) {
				$models[$tag->id] = $tag;
			}
		}
		return $models;
	}

	/**
	 * @param IssueTag[] $tags
	 * @param string $position
	 * @return IssueTag[]
	 */
	public static function linkIssuesGridPositionFilter(array $tags, string $position): array {
		$models = [];
		foreach ($tags as $tag) {
			if ($tag->tagType && $tag->tagType->link_issues_grid_position === $position) {
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

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'background', 'color'], 'required'],
			[['sort_order'], 'integer'],
			[['name', 'background', 'color', 'css_class', 'view_issue_position', 'issues_grid_position', 'link_issues_grid_position'], 'string', 'max' => 255],
			[['background', 'color', 'css_class', 'view_issue_position', 'issues_grid_position', 'link_issues_grid_position'], 'default', 'value' => null],
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
			'view_issue_position' => Yii::t('issue', 'View Issue Position'),
			'viewIssuePositionName' => Yii::t('issue', 'View Issue Position'),
			'issues_grid_position' => Yii::t('issue', 'Issues_Grid Position'),
			'issuesGridPositionName' => Yii::t('issue', 'Issues_Grid Position'),
			'link_issues_grid_position' => Yii::t('issue', 'Link Issues Grid Position'),
			'linkIssuesGridPositionName' => Yii::t('issue', 'Link Issues Grid Position'),
			'tagsCount' => Yii::t('common', 'Tags Count'),
			'issuesCount' => Yii::t('issue', 'Issues Count'),
			'sort_order' => Yii::t('common', 'Sort order'),
		];
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(
			IssueTagType::find()->orderBy([
				'sort_order' => SORT_ASC,
				'name' => SORT_ASC,
			])->all(),
			'id',
			'name'
		);
	}

	/**
	 * Gets query for [[IssueTags]].
	 *
	 * @return ActiveQuery
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

	public function getLinkIssuesGridPositionName(): ?string {
		return static::getLinkIssuesGridPositionNames()[$this->link_issues_grid_position];
	}

	public static function getLinkIssuesGridPositionNames(): array {
		return [
			static::LINK_ISSUES_GRID_POSITION_COLUMN_ISSUE_BOTTOM => Yii::t('issue', 'Link Issues Grid Position - Issue'),
			static::LINK_ISSUES_GRID_POSITION_COLUMN_CUSTOMER_BOTTOM => Yii::t('issue', 'Link Issues Grid Position - Customer'),
		];
	}

	public function getViewIssuePositionName(): ?string {
		return static::getViewIssuePositionNames()[$this->view_issue_position];
	}

	public static function getViewIssuePositionNames(): array {
		return [
			static::VIEW_ISSUE_BEFORE_CUSTOMERS => Yii::t('issue', 'Issue View Position - Before Customers'),
			static::VIEW_ISSUE_POSITION_CUSTOMER => Yii::t('issue', 'Issue View Position - Customer'),
			static::VIEW_ISSUE_POSITION_AFTER_DETAILS => Yii::t('issue', 'Issue View Position - After Details'),
			static::VIEW_ISSUE_POSITION_BEFORE_DETAILS => Yii::t('issue', 'Issue View Position - Before Details'),

		];
	}

	public function getIssuesGridPositionName(): ?string {
		return static::getIssuesGridPositionNames()[$this->issues_grid_position];
	}

	public static function getIssuesGridPositionNames(): array {
		return [
			static::ISSUES_GRID_POSITION_COLUMN_ISSUE_BOTTOM => Yii::t('issue', 'Issues Grid Position - Issue -> bottom'),
			static::ISSUES_GRID_POSITION_COLUMN_CUSTOMER_BOTTOM => Yii::t('issue', 'Issues Grid Position - Customer -> bottom'),
			static::ISSUES_GRID_POSITION_COLUMN_STAGE_BOTTOM => Yii::t('issue', 'Issues Grid Position - Type -> bottom'),
			static::ISSUES_GRID_POSITION_COLUMN_TYPE_BOTTOM => Yii::t('issue', 'Issues Grid Position - Stage -> bottom'),

		];
	}
}

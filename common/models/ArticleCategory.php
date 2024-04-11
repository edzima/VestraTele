<?php

namespace common\models;

use common\models\query\ArticleCategoryQuery;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%article_category}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $comment
 * @property integer $parent_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Article[] $articles
 * @property ArticleCategory $parent
 * @property ArticleCategory[] $childs
 */
class ArticleCategory extends ActiveRecord {

	public const STATUS_DRAFT = 0;
	public const STATUS_ACTIVE = 1;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%article_category}}';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			TimestampBehavior::class,
			[
				'class' => SluggableBehavior::class,
				'attribute' => 'title',
				'ensureUnique' => true,
				'immutable' => true,
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			['title', 'required'],
			['comment', 'string'],
			[['parent_id', 'status', 'created_at', 'updated_at'], 'integer'],
			[['title', 'slug'], 'string', 'max' => 255],
			['parent_id', 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['parent_id' => 'id']],
			['status', 'default', 'value' => self::STATUS_DRAFT],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'title' => Yii::t('common', 'Title'),
			'slug' => Yii::t('common', 'Slug'),
			'comment' => Yii::t('common', 'Comment'),
			'parent_id' => Yii::t('common', 'Parent category'),
			'status' => Yii::t('common', 'Status'),
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getArticles() {
		return $this->hasMany(Article::class, ['category_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getParent() {
		return $this->hasOne(static::class, ['id' => 'parent_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getChilds() {
		return $this->hasMany(static::class, ['parent_id' => 'id']);
	}

	/**
	 * @inheritdoc
	 * @return ArticleCategoryQuery the active query used by this AR class.
	 */
	public static function find() {
		return new ArticleCategoryQuery(static::class);
	}
}

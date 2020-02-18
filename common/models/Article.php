<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\query\ArticleQuery;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property integer $status
 * @property integer $category_id
 * @property integer $author_id
 * @property integer $updater_id
 * @property integer $published_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 *
 * @property User $author
 * @property ArticleCategory $category
 * @property User $updater
 */
class Article extends ActiveRecord {

	public const STATUS_DRAFT = 0;
	public const STATUS_ACTIVE = 1;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%article}}';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			TimestampBehavior::class,
			[
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'author_id',
				'updatedByAttribute' => 'updater_id',
			],
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
	public function rules():array {
		return [
			[['title', 'body', 'category_id'], 'required'],
			[['body'], 'string'],
			[
				'published_at', 'default',
				'value' => static function () {
					return date(DATE_ATOM);
				},
			],
			['published_at', 'filter', 'filter' => 'strtotime'],
			[['status', 'category_id', 'author_id', 'updater_id', 'created_at', 'updated_at'], 'integer'],
			[['title', 'slug'], 'string', 'max' => 255],
			['status', 'default', 'value' => self::STATUS_DRAFT],
			['author_id', 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
			['category_id', 'exist', 'skipOnError' => true, 'targetClass' => ArticleCategory::class, 'targetAttribute' => ['category_id' => 'id']],
			['updater_id', 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updater_id' => 'id']],

		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'title' => Yii::t('common', 'Title'),
			'slug' => Yii::t('common', 'Slug'),
			'body' => Yii::t('common', 'Text'),
			'status' => Yii::t('common', 'Status'),
			'category_id' => Yii::t('common', 'Category'),
			'author_id' => Yii::t('common', 'Author'),
			'updater_id' => Yii::t('common', 'Updater'),
			'published_at' => Yii::t('common', 'Published at'),
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'tagValues' => Yii::t('common', 'Tags'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function transactions() {
		return [
			self::SCENARIO_DEFAULT => self::OP_ALL,
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAuthor() {
		return $this->hasOne(User::className(), ['id' => 'author_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCategory() {
		return $this->hasOne(ArticleCategory::className(), ['id' => 'category_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUpdater() {
		return $this->hasOne(User::className(), ['id' => 'updater_id']);
	}

	/**
	 * @inheritdoc
	 * @return \common\models\query\ArticleQuery the active query used by this AR class.
	 */
	public static function find() {
		return new ArticleQuery(get_called_class());
	}
}

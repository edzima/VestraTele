<?php

namespace common\models;

use common\models\query\ArticleQuery;
use common\models\user\User;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $preview
 * @property string $body
 * @property integer $status
 * @property integer $category_id
 * @property integer $author_id
 * @property integer $updater_id
 * @property integer $published_at
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $show_on_mainpage
 *
 *
 * @property-read User $author
 * @property-read ArticleCategory $category
 * @property-read User $updater
 * @property-read ArticleUser[] $articleUsers
 */
class Article extends ActiveRecord {

	public const STATUS_DRAFT = 0;
	public const STATUS_ACTIVE = 1;

	public array $usersIds = [];

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%article}}';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors(): array {
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
			[['title', 'body', 'category_id'], 'required'],
			[['preview', 'body'], 'string'],
			[
				'published_at', 'default',
				'value' => static function () {
					return date(DATE_ATOM);
				},
			],
			['published_at', 'filter', 'filter' => 'strtotime'],
			[['status', 'category_id', 'author_id', 'updater_id', 'created_at', 'updated_at', 'show_on_mainpage'], 'integer'],
			[['title', 'slug'], 'string', 'max' => 255],
			['show_on_mainpage', 'default', 'value' => null],
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
			'preview' => Yii::t('common', 'Preview'),
			'body' => Yii::t('common', 'Text'),
			'status' => Yii::t('common', 'Status'),
			'category_id' => Yii::t('common', 'Category'),
			'author_id' => Yii::t('common', 'Author'),
			'updater_id' => Yii::t('common', 'Updater'),
			'published_at' => Yii::t('common', 'Published at'),
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'tagValues' => Yii::t('common', 'Tags'),
			'show_on_mainpage' => Yii::t('common', 'Show on Mainpage'),
		];
	}

	public function isForUser(int $userId): bool {
		$users = $this->articleUsers;
		if (empty($users)) {
			return true;
		}
		foreach ($users as $user) {
			if ($user->user_id === $userId) {
				return true;
			}
		}
		return false;
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
	 * @return ActiveQuery
	 */
	public function getAuthor() {
		return $this->hasOne(User::class, ['id' => 'author_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getCategory() {
		return $this->hasOne(ArticleCategory::class, ['id' => 'category_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getUpdater() {
		return $this->hasOne(User::class, ['id' => 'updater_id']);
	}

	public function getArticleUsers() {
		return $this->hasMany(ArticleUser::class, [
			'article_id' => 'id',
		]);
	}

	public static function find(): ArticleQuery {
		return new ArticleQuery(static::class);
	}
}

<?php

namespace common\models;

use common\models\user\User;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "article_user".
 *
 * @property int $user_id
 * @property int $article_id
 *
 * @property Article $article
 * @property User $user
 */
class ArticleUser extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%article_user}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'article_id'], 'required'],
			[['user_id', 'article_id'], 'integer'],
			[['user_id', 'article_id'], 'unique', 'targetAttribute' => ['user_id', 'article_id']],
			[['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::class, 'targetAttribute' => ['article_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'user_id' => 'User ID',
			'article_id' => 'Article ID',
		];
	}

	/**
	 * Gets query for [[Article]].
	 *
	 * @return ActiveQuery
	 */
	public function getArticle() {
		return $this->hasOne(Article::class, ['id' => 'article_id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public static function batchInsert(array $usersIds, int $articleId): void {
		$rows = [];
		foreach ($usersIds as $id) {
			$rows[] = [
				'user_id' => $id,
				'article_id' => $articleId,
			];
		}
		if (!empty($rows)) {
			static::getDb()
				->createCommand()
				->batchInsert(static::tableName(), [
					'user_id',
					'article_id',
				], $rows
				)->execute();
		}
	}
}

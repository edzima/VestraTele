<?php

namespace backend\models;

use common\helpers\ArrayHelper;
use common\models\Article;
use common\models\ArticleCategory;
use common\models\ArticleUser;
use common\models\user\User;
use Yii;
use yii\base\Model;

class ArticleForm extends Model {

	private ?Article $model = null;
	public $usersIds = [];
	public string $title = '';
	public string $slug = '';
	public string $preview = '';
	public int $status = Article::STATUS_ACTIVE;
	public $published_at;
	public string $body = '';
	public ?int $updater_id = null;
	public $show_on_mainpage;
	public ?int $author_id = null;
	public ?int $category_id = null;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['title', 'body', 'category_id', 'author_id'], 'required'],
			[['preview', 'body'], 'string'],
			['published_at', 'filter', 'filter' => 'strtotime'],
			[['show_on_mainpage'], 'default', 'value' => null],

			[['status', 'category_id', 'show_on_mainpage'], 'integer'],
			[['title', 'slug'], 'string', 'max' => 255],
			['status', 'default', 'value' => Article::STATUS_DRAFT],
			['category_id', 'exist', 'skipOnError' => true, 'targetClass' => ArticleCategory::class, 'targetAttribute' => ['category_id' => 'id']],
			['usersIds', 'in', 'range' => array_keys($this->getUsersNames()), 'allowArray' => true],
		];
	}

	public function attributeLabels(): array {
		return array_merge(
			Article::instance()->attributeLabels(), [
				'usersIds' => Yii::t('backend', 'Visible for'),
			]
		);
	}

	public function setModel(Article $model): void {
		$this->model = $model;
		$this->title = $model->title;
		$this->slug = $model->slug;
		$this->show_on_mainpage = $model->show_on_mainpage;
		$this->status = $model->status;
		$this->published_at = $model->published_at;
		$this->body = $model->body;
		$this->preview = $model->preview;
		$this->author_id = $model->author_id;
		$this->updater_id = $model->updater_id;
		$this->category_id = $model->category_id;
		$this->usersIds = ArrayHelper::getColumn($model->articleUsers, 'user_id');
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->title = $this->title;
		$model->author_id = $this->author_id;
		$model->slug = $this->slug;
		$model->show_on_mainpage = $this->show_on_mainpage;
		$model->status = $this->status;
		$model->published_at = $this->published_at;
		$model->body = $this->body;
		$model->preview = $this->preview;
		$model->updater_id = $this->updater_id;
		$model->category_id = $this->category_id;
		if (!$model->save()) {
			return false;
		}
		$this->linkUsers();
		return true;
	}

	public function getModel(): Article {
		if ($this->model === null) {
			$this->model = new Article();
		}
		return $this->model;
	}

	private function linkUsers(): void {
		$articleId = $this->getModel()->id;
		if ($articleId) {
			if (!$this->getModel()->isNewRecord) {
				ArticleUser::deleteAll(['article_id' => $articleId]);
			}
			if (!empty($this->usersIds)) {
				ArticleUser::batchInsert($this->usersIds, $articleId);
			}
		}
	}

	public function getUsersNames(): array {
		return User::getSelectList(
			User::getAssignmentIds([User::PERMISSION_NEWS])
		);
	}

	public function getCategoriesNames(): array {
		return ArrayHelper::map(
			ArticleCategory::find()->active()->all(),
			'id',
			'title'
		);
	}
}

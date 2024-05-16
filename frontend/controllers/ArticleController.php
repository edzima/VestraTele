<?php

namespace frontend\controllers;

use common\models\Article;
use common\models\ArticleCategory;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class ArticleController.
 */
class ArticleController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	/**
	 * Lists all Article models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$query = Article::find()
			->published()
			->forUser(Yii::$app->user->getId())
			->with('category');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'defaultPageSize' => 10,
			],
		]);

		$dataProvider->sort = [
			'defaultOrder' => ['created_at' => SORT_DESC],
		];

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'menuItems' => self::getMenuItems(),
		]);
	}

	/**
	 * Displays a single Article model.
	 *
	 * @param $slug
	 * @return mixed
	 */
	public function actionView($slug): string {
		$model = Article::find()->andWhere(['slug' => $slug])->published()->one();
		if (!$model) {
			throw new NotFoundHttpException(Yii::t('frontend', 'Page not found.'));
		}
		if (!$model->isForUser(Yii::$app->user->getId())) {
			throw new NotFoundHttpException(Yii::t('frontend', 'Page not found.'));
		}

		return $this->render('view', [
			'model' => $model,
			'menuItems' => self::getMenuItems(),
		]);
	}

	/**
	 * Lists all Article models that are category with $slug.
	 *
	 * @param $slug
	 * @return mixed
	 */
	public function actionCategory(string $slug): string {
		$model = ArticleCategory::find()->andWhere(['slug' => $slug])->active()->one();
		if (!$model) {
			throw new NotFoundHttpException(Yii::t('frontend', 'Page not found.'));
		}

		$query = Article::find()
			->joinWith('category')->where('{{%article_category}}.slug = :slug', [':slug' => $slug])
			->published()
			->forUser(Yii::$app->user->getId());

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'defaultPageSize' => 10,
			],
		]);

		$dataProvider->sort = [
			'defaultOrder' => ['created_at' => SORT_DESC],
		];

		return $this->render('category', [
			'model' => $model,
			'dataProvider' => $dataProvider,
			'menuItems' => self::getMenuItems(),
		]);
	}

	/**
	 * Generate menu items for yii\widgets\Menu
	 *
	 * @param null|ArticleCategory[] $models
	 * @return array
	 */
	public static function getMenuItems(array $models = null): array {
		$items = [];
		if ($models === null) {
			$models = ArticleCategory::find()->where(['parent_id' => null])->with('childs')->orderBy(['id' => SORT_ASC])->active()->all();
		}
		foreach ($models as $model) {
			$items[] = [
				'url' => ['article/category', 'slug' => $model->slug],
				'label' => $model->title,
				'items' => self::getMenuItems($model->childs),
			];
		}

		return $items;
	}
}

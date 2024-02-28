<?php

use backend\helpers\Html;
use backend\models\search\ArticleSearch;
use backend\widgets\GridView;
use common\helpers\ArrayHelper;
use common\models\Article;
use common\models\ArticleCategory;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\SerialColumn;

/* @var $this yii\web\View */
/* @var $searchModel ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Articles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

	<p>
		<?= Html::a(Yii::t('backend', 'Create article'), ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('backend', 'Category'), ['article-category/index'], ['class' => 'btn btn-info']) ?>

	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => SerialColumn::class],

			//'id',
			'title',
			// 'slug',
			// 'description',
			// 'keywords',
			// 'body:ntext',
			[
				'attribute' => 'status',
				'format' => 'html',
				'value' => function ($model) {
					return $model->status ? '<span class="glyphicon glyphicon-ok text-success"></span>' : '<span class="glyphicon glyphicon-remove text-danger"></span>';
				},
				'filter' => [
					Article::STATUS_DRAFT => Yii::t('backend', 'Not active'),
					Article::STATUS_ACTIVE => Yii::t('backend', 'Active'),
				],
			],
			'show_on_mainpage',
			[
				'attribute' => 'category_id',
				'value' => function ($model) {
					return $model->category ? $model->category->title : null;
				},
				'filter' => ArrayHelper::map(ArticleCategory::find()->all(), 'id', 'title'),
			],
			[
				'attribute' => 'author_id',
				'value' => 'author',
				'filter' => ArticleSearch::authorNames(),
			],
			// 'updater_id',
			'published_at:datetime',
			'created_at:datetime',
			'updated_at:datetime',
			[
				'attribute' => 'user_id',
				'value' => function (Article $data): string {
					$users = $data->articleUsers;
					if ($users) {
						$names = ArrayHelper::getColumn($users, 'user');
						return Html::ul($names);
					}
					return Yii::t('backend', 'All Users');
				},
				'format' => 'html',
				'label' => Yii::t('backend', 'Visible for'),
			],
			[
				'class' => ActionColumn::class,
				'template' => '{update} {delete}',
			],
		],
	]) ?>

</div>

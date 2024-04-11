<?php

use common\models\Article;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-view">
	<article class="article-item">
		<h1><?= Html::encode($this->title) ?></h1>

		<div class="article-meta">
			<span class="glyphicon glyphicon-time"></span> <?= Yii::$app->formatter->asDatetime($model->published_at) ?>
			<span class="glyphicon glyphicon-folder-close"></span> <?= Html::a($model->category->title, ['article/category', 'slug' => $model->category->slug]) ?>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="article-text">
					<?= HtmlPurifier::process($model->body, function ($config) {
						$config->set('HTML.SafeIframe', true);
						$config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');
					}) ?>
				</div>
				<hr/>
			</div>
		</div>
	</article>
</div>


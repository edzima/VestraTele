<?php

use common\models\Article;
use frontend\helpers\Html;
use yii\helpers\HtmlPurifier;

/** @var Article $model */
?>

<div class="article-view">
	<article class="article-item">
		<h2><?= Html::encode($model->title) ?></h2>

		<div class="article-meta">
			<span class="glyphicon glyphicon-time"></span> <?= Yii::$app->formatter->asDatetime($model->updated_at) ?>
			<span class="glyphicon glyphicon-folder-close"></span> <?= Html::a($model->category->title, ['article/category', 'slug' => $model->category->slug]) ?>
		</div>


		<div>
			<div class="article-text">
				<?= HtmlPurifier::process($model->body, static function ($config) {
					$config->set('HTML.SafeIframe', true);
					$config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');
				}) ?>
			</div>
			<hr/>
		</div>
	</article>
</div>



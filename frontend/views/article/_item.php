<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use common\assets\Highlight;

/**
 * @var yii\web\View
 * @var common\models\Article
 */

Highlight::register($this);
?>

<hr/>
<div class="article-item">
    <h2 class="article-title">
        <?= Html::a($model->title, ['view', 'slug' => $model->slug]) ?>
    </h2>

    <div class="article-meta">
        <span class="glyphicon glyphicon-time"></span> <?= Yii::$app->formatter->asDatetime($model->published_at) ?>
        <span class="glyphicon glyphicon-folder-close"></span> <?= Html::a($model->category->title, ['article/category', 'slug' => $model->category->slug]) ?>
    </div>

    <div class="article-text">
        <?= HtmlPurifier::process(Yii::t('frontend'.$model->title, 'description')) ?>
    </div>
	<div class="article-span">
			<button type="button" class="btn btn-primary">
				<?=$model->start_at?><span class="glyphicon glyphicon-triangle-left" aria-hidden="true"></span>
			</button>
			<button id="threshold" type="button" class="btn btn-success" value="<?=$model->point?>">
				<span class="glyphicon glyphicon-screenshot" aria-hidden="true"></span>  Próg punktowy : <?=$model->point?>
			</button>
				<button type="button" class="btn btn-primary">
				<span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span><?=$model->finish_at?>
			</button>	
	</div>
</div>

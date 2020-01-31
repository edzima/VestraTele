<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/**
 * @var yii\web\View
 * @var common\models\Article
 */ 

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
</div>

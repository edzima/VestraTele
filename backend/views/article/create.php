<?php

/* @var $this yii\web\View */

/* @var $model ArticleForm */

use backend\models\ArticleForm;

$this->title = Yii::t('backend', 'Create article');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

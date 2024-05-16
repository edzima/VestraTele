<?php

use backend\models\ArticleForm;

/* @var $this yii\web\View */
/* @var $model ArticleForm */

$this->title = Yii::t('backend', 'Update article: {title}', ['title' => $model->title]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="article-update">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

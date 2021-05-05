<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\hint\HintSource */

$this->title = Yii::t('hint', 'Update Hint Source: {name}', [
	'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hint', 'Hint Sources'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('hint', 'Update');
?>
<div class="hint-source-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

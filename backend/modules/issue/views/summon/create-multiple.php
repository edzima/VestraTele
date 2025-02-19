<?php

use backend\modules\issue\models\SummonForm;

/* @var $this yii\web\View */
/* @var $model SummonForm */

$this->title = $model->getType()
	? Yii::t('backend', 'Create Summons: {type} for Issues: {count}', [
		'type' => $model->getType()->name,
		'count' => count($model->issuesIds),
	])
	: Yii::t('backend', 'Create Summons for Issues: {count}', [
		'count' => count($model->issuesIds),
	]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->getType()
	? Yii::t('backend', 'Create Summons: {type}', [
		'type' => $model->getType()->name,
	])
	: Yii::t('backend', 'Create Summons');
?>
<div class="summon-create-multiple">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

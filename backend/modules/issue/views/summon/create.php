<?php

use backend\modules\issue\models\SummonForm;

/* @var $this yii\web\View */
/* @var $model SummonForm */

$this->title = $model->getType()
	? Yii::t('backend', 'Create Summon: {type}', [
		'type' => $model->getType()->name,
	])
	: Yii::t('backend', 'Create summon');

$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summon-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

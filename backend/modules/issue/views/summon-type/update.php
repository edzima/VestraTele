<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\issue\SummonType */

$this->title = Yii::t('backend', 'Update Summon Type: {name}', ['name' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['/issue/summon/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Summon Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="summon-type-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

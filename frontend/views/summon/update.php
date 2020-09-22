<?php

use frontend\models\SummonForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model SummonForm */

$this->title = Yii::t('common', 'Update Summon: {title}', ['title' => $model->getModel()->title]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->issue, 'url' => ['/issue/view', 'id' => $model->getModel()->issue_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->title, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="summon-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

<?php

use backend\modules\issue\models\SummonForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model SummonForm */

$this->title = Yii::t('common', 'Update Summon: {title}', ['title' => $model->title]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="summon-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

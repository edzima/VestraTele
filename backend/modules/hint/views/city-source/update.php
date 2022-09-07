<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\hint\HintCitySource */

$this->title = Yii::t('hint', 'Update Hint City Source: {name}', [
	'name' => $model->hint->getCityNameWithType(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hint', 'Hint Cities'), 'url' => ['city/index']];
$this->params['breadcrumbs'][] = ['label' => $model->hint->getCityNameWithType(), 'url' => ['city/view', 'id' => $model->hint_id]];
$this->params['breadcrumbs'][] = Yii::t('hint', 'Update');
?>
<div class="hint-city-source-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

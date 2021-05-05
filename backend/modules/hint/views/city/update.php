<?php

use backend\modules\hint\models\HintCityForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model HintCityForm */

$this->title = Yii::t('hint', 'Update Hint City: {name}', [
	'name' => $model->getModel()->getCityNameWithType(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hint', 'Hint Cities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getCityNameWithType(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('hint', 'Update');
?>
<div class="hint-city-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

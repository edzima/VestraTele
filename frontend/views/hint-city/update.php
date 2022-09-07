<?php

use frontend\helpers\Html;
use frontend\models\HintCityForm;

/* @var $this \yii\web\View */
/* @var $model HintCityForm */

$this->title = Yii::t('hint', 'Update Hint: {name}', ['name' => $model->getModel()->getCityNameWithType()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hint', 'Hint Cities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getCityNameWithType(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');

?>

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', [
	'model' => $model,
]) ?>

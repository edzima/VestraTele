<?php

use common\models\address\City;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model City */

$this->title = 'Edycja Miejscowość: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edycja';
?>
<div class="city-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

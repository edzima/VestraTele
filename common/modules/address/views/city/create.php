<?php

use common\models\address\City;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model City */

$this->title = 'Dodaj Miejscowość';
$this->params['breadcrumbs'][] = ['label' => 'Miejscowości', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

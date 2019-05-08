<?php

use common\models\Wojewodztwa;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Wojewodztwa */

$this->title = 'Edytuj Region: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Regiony', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edycja';
?>
<div class="state-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

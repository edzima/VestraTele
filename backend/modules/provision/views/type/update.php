<?php

use backend\modules\provision\models\ProvisionTypeForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ProvisioNTypeForm */

$this->title = 'Edytuj typ prowizji: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Typy', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="provision-type-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

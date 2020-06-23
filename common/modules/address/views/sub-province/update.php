<?php

use common\models\address\SubProvince;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model SubProvince */

$this->title = 'Edytuj Gmine: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Gminy', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edycja';
?>
<div class="sub-state-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

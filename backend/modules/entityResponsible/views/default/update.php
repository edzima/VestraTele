<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\entityResponsible\EntityResponsible */

$this->title = 'Edytuj: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Podmioty odpowiedzialne', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="issue-entity-responsible-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

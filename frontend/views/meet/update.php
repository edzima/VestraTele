<?php

use frontend\models\meet\MeetForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model MeetForm */

$this->title = 'Edytyj lead: ' . $model->getName();
$this->params['breadcrumbs'][] = ['label' => 'Lead', 'url' => 'index'];
$this->params['breadcrumbs'][] = ['label' => $model->getName(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edytuj';
?>
<div class="issue-meet-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

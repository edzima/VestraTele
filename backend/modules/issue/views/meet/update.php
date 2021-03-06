<?php

use backend\modules\issue\models\MeetForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model MeetForm */

$this->title = 'Edytyj spotkanie: ' . $model->getName();
$this->params['breadcrumbs'][] = ['label' => 'Spotkania', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edytuj';
?>
<div class="issue-meet-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

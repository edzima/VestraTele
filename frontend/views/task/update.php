<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Task */

$this->title = 'Edycja spotkania: ' . $model->victim_name;
$this->params['breadcrumbs'][] = ['label' => 'Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="task-update">

    <h1><?= Html::encode($this->title) ?></h1>

      <?= $this->render('_form', [
        'model' => $model,
		'woj' => $woj,
		'accident' =>$accident,
		'agent' => $agent,
    ]) ?>

</div>

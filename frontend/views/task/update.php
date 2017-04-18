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
    <p>
        <?= Html::a('Usuń', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Jesteś pewny, że chcesz usunąć ten wpis?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

      <?= $this->render('_form', [
        'model' => $model,
		'woj' => $woj,
		'accident' =>$accident,
		'agent' => $agent,
		'powiat' => $powiat,
		'gmina' => $gmina,
		'city' => $city
    ]) ?>

</div>

<?php

use yii\helpers\Html;



/* @var $this yii\web\View */
/* @var $model common\models\Task */

$this->title = 'Nowe spotkanie';

$this->params['breadcrumbs'][] = ['label' => 'Spotkania', 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
		'woj' => $woj,
		'accident' =>$accident,
		'agent' => $agent,
    ]) ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TaskStatus */

$this->title = 'Raport sprawy '.$task->id;
$this->params['breadcrumbs'][] = ['label' => 'Spotkania', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-status-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
		'task' =>$task,
		'answers' =>$answers,

    ]) ?>

</div>

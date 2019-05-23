<?php

use common\models\AnswerTyp;
use common\models\Task;
use common\models\TaskStatus;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model TaskStatus */
/* @var $task Task */
/* @var $answers AnswerTyp[] */
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

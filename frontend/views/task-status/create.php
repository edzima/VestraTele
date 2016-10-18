<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TaskStatus */

$this->title = 'Create Task Status';
$this->params['breadcrumbs'][] = ['label' => 'Task Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-status-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

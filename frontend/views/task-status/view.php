<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TaskStatus */

$this->title = 'Raport sprawy nr: '.$model->task_id;
$this->params['breadcrumbs'][] = ['label' => 'Twoje spotkania', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-status-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'task_id',
            [
				 'attribute' => 'answer',
				 'value' => $model->answer->name,
				 'label' => 'Efekt spotkania',
			],
            'count_agreement',
            'status_details:ntext',
            'name',
			'finished:boolean',
			'extra_agreement',
			'extra_name'
        ],
    ]) ?>

</div>

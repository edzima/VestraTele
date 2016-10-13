<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Task */

$this->title = 'Spotkanie nr: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Spotkania', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Edytuj', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Usuń', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Jesteś pewny, że chcesz usunąć ten wpis?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            //'tele_id',
            [
				 'attribute' => 'agent_id',
				 'value' => $model->agent->username,
			],
		
            'victim_name',
            'phone',
            'created_at',
            'updated_at',
            [
				 'attribute' => 'accident_id',
				 'value' => $model->accident->name,
			],
			[
				 'attribute' => 'woj',
				 'value' => $model->wojewodztwo->name,
			],
            [
				 'attribute' => 'powiat',
				 'value' => $model->powiatRel->name,
			],
            [
				 'attribute' => 'gmina',
				 'value' => $model->gminaRel->name,
			],
            [
				 'attribute' => 'city',
				 'value' => $model->miasto->name,
			],
            'qualified_name',
            'details:ntext',
            'meeting:boolean',
            'date',
        ],
    ]) ?>

</div>

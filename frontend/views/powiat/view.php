<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Powiat */

$this->title = $model->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="powiat-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Edytuj', ['update', 'id' => $model->id, 'wojewodztwo_id' => $model->wojewodztwo_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Usuń', ['delete', 'id' => $model->id, 'wojewodztwo_id' => $model->wojewodztwo_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Jesteś pewny, że chcesz usunąć?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
				'attribute'=>'wojewodztwo_id',
				'value' => $model->wojewodztwo->name
			],
            'name',
        ],
    ]) ?>

</div>

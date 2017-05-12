<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Cause */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Causes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cause-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('frontend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('frontend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('frontend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'victim_name',
            [
                'attribute' => 'author',
                'value' => $model->author->username,
            ],
            [
                'attribute' => 'category_id',
                'value' => $model->category->name,
            ],
            'created_at:datetime',
            'updated_at:datetime',
            'date:datetime',
            'is_finished:boolean',
        ],
    ]) ?>

</div>

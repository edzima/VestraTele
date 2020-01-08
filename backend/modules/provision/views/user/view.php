<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\provision\ProvisionUser */

$this->title = $model->from_user_id;
$this->params['breadcrumbs'][] = ['label' => 'Provision Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="provision-user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'from_user_id' => $model->from_user_id, 'to_user_id' => $model->to_user_id, 'type_id' => $model->type_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'from_user_id' => $model->from_user_id, 'to_user_id' => $model->to_user_id, 'type_id' => $model->type_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'from_user_id',
            'to_user_id',
            'type_id',
            'value',
        ],
    ]) ?>

</div>

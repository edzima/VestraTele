<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\settlement\PayReceived */

$this->title = $model->pay_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Pay Receiveds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pay-received-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('settlement', 'Update'), ['update', 'id' => $model->pay_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('settlement', 'Delete'), ['delete', 'id' => $model->pay_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('settlement', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'pay_id',
            'user_id',
            'created_at',
            'transfer_at',
        ],
    ]) ?>

</div>

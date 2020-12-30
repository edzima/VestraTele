<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\settlement\PayReceived */

$this->title = Yii::t('settlement', 'Update Pay Received: {name}', [
    'name' => $model->pay_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Pay Receiveds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pay_id, 'url' => ['view', 'id' => $model->pay_id]];
$this->params['breadcrumbs'][] = Yii::t('settlement', 'Update');
?>
<div class="pay-received-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

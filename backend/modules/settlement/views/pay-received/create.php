<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\settlement\PayReceived */

$this->title = Yii::t('settlement', 'Create Pay Received');
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Pay Receiveds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pay-received-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

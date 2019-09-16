<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\benefit\BenefitAmount */

$this->title = 'Update Benefit Amount: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Benefit Amounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="benefit-amount-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

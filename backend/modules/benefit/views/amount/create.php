<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\benefit\BenefitAmount */

$this->title = 'Create Benefit Amount';
$this->params['breadcrumbs'][] = ['label' => 'Benefit Amounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="benefit-amount-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

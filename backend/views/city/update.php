<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\City */

$this->title = 'Edycja Mijescowości: ' . $city->name;
$this->params['breadcrumbs'][] = ['label' => 'Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $city->name, 'url' => ['view', 'id' => $city->id]];
$this->params['breadcrumbs'][] = 'Edycja';
?>
<div class="city-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'city' => $city,
    ]) ?>

</div>

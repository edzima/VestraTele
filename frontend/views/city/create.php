<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\City */

$this->title = 'Dodaj Miejscowość';
$this->params['breadcrumbs'][] = ['label' => 'Miejscowości', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'city' => $city,
    ]) ?>

</div>

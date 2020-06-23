<?php

use common\models\address\State;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model State */

$this->title = 'Dodaj region';
$this->params['breadcrumbs'][] = ['label' => 'Regiony', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="state-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

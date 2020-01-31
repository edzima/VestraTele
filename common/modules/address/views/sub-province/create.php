<?php

use common\models\Wojewodztwa;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model Wojewodztwa */

$this->title = 'Dodaj Gmine';
$this->params['breadcrumbs'][] = ['label' => 'Gminy', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-province-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

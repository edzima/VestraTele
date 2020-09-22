<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\issue\Summon */

$this->title = Yii::t('backend', 'Create Summon');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summon-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

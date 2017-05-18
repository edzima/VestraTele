<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CauseCategory */

$this->title = Yii::t('frontend', 'Update') .": ". $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Cause Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="cause-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

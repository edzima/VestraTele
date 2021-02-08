<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\Lead */

$this->title = Yii::t('lead', 'Update Lead: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

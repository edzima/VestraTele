<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\LeadReportSchema */

$this->title = Yii::t('lead', 'Create Lead Report Schema');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Report Schemas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-report-schema-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use common\modules\lead\models\forms\LeadForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $id int */
/* @var $model LeadForm */

$this->title = Yii::t('lead', 'Update Lead: {name}', [
	'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $id]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

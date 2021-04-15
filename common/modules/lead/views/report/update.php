<?php

use common\modules\lead\models\forms\LeadReportForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadReportForm */

$this->title = Yii::t('lead', 'Update Lead Report: {name}', [
	'name' => $model->getModel()->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->lead_id, 'url' => ['lead/view', 'id' => $model->getModel()->lead_id]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-report-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

<?php

/* @var $this \yii\web\View */
/* @var $model \common\modules\lead\models\forms\LeadForm */

/* @var $report \common\modules\lead\models\forms\ReportForm */

use common\helpers\Html;

$this->title = Yii::t('lead', 'Create Lead: {source}', ['source' => $model->getSourcesNames()[$model->source_id]]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="lead-create-from-source">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form_report', [
		'model' => $model,
		'report' => $report,
	]) ?>

</div>


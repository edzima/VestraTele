<?php

/* @var $this View */
/* @var $model LeadForm */

/* @var $report ReportForm */

use common\helpers\Html;
use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\forms\ReportForm;
use yii\web\View;

$this->title = Yii::t('lead', 'Create Lead: {source}', ['source' => $model->getSourcesNames()[$model->source_id]]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="lead-create-from-source">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form_report', [
		'model' => $model,
		'isCreateForm' => true,
		'report' => $report,
	]) ?>

</div>


<?php

/* @var $this View */
/* @var $model LeadForm */
/* @var $type LeadType */

/* @var $report ReportForm */

use common\helpers\Html;
use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\models\LeadType;
use yii\web\View;

$this->title = Yii::t('lead', 'Create Lead: {type}', ['type' => $type->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
Yii::warning($model->getErrors());
Yii::warning($report->getErrors());
?>


<div class="lead-create-from-type">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form_report', [
		'model' => $model,
		'isCreateForm' => true,
		'report' => $report,
	]) ?>

</div>


<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\models\LeadType;
use yii\web\View;

/* @var $this View */
/* @var $lead ActiveLead */
/* @var $model LeadForm */
/* @var $report ReportForm */
/* @var $type LeadType */

$this->title = Yii::t('lead', 'Copy Lead to {type}: {lead}', ['type' => $type->getName(), 'lead' => $lead->getName()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $lead->getName(), 'url' => ['view', 'id' => $lead->getId()]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="lead-copy">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form_report', [
		'model' => $model,
		'report' => $report,
	]) ?>

</div>


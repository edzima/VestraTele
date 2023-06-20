<?php

use common\modules\lead\models\forms\LeadReminderForm;

/* @var $this yii\web\View */
/* @var $model LeadReminderForm $model */

$lead = $model->getLead();

$this->title = Yii::t('lead', 'Create Reminder for Lead: {lead}', ['lead' => $lead->getName()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $lead->getName(), 'url' => ['lead/view', 'id' => $lead->getId()]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Reminders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-reminder-create">

	<?= $this->render('form', [
		'model' => $model,
	]) ?>

</div>

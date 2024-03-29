<?php

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\LeadReminderForm;

/* @var $this yii\web\View */
/* @var $model LeadReminderForm */
/* @var $lead ActiveLead */

$this->title = Yii::t('lead', 'Update Reminder for Lead: {lead}', ['lead' => $lead->getName()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $lead->getName(), 'url' => ['lead/view', 'id' => $lead->getId()]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Reminders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="lead-reminder-update">
	<?= $this->render('form', [
		'model' => $model,
	]) ?>
</div>

<?php

use common\modules\lead\models\forms\LeadReminderForm;
use common\modules\reminder\widgets\ReminderFormWidget;
use yii\helpers\Html;

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

	<h1><?= Html::encode($this->title) ?></h1>

	<?= ReminderFormWidget::widget([
		'model' => $model,
		'users' => $model->getUsersNames(),
	]) ?>
</div>

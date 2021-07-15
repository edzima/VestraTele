<?php

use common\modules\lead\models\ActiveLead;
use common\modules\reminder\models\ReminderForm;
use common\modules\reminder\widgets\ReminderFormWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ReminderForm */
/* @var $lead ActiveLead */

$this->title = Yii::t('lead', 'Create Reminder for Lead: #{id}', ['id' => $lead->getId()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $lead->getId(), 'url' => ['lead/view', 'id' => $lead->getId()]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Reminders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-reminder-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= ReminderFormWidget::widget([
		'model' => $model,
	]) ?>
</div>

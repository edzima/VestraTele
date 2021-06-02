<?php

use common\modules\lead\models\ActiveLead;
use common\modules\reminder\models\ReminderForm;
use common\modules\reminder\widgets\ReminderFormWidget;

/* @var $this \yii\web\View */
/* @var $model ReminderForm */
/* @var $lead ActiveLead */

$this->title = Yii::t('lead', 'Create Reminder for Lead: #{id}', ['id' => $lead->getId()]);
?>


<?= ReminderFormWidget::widget([
	'model' => $model,
]) ?>

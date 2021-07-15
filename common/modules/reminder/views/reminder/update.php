<?php

use common\modules\reminder\models\ReminderForm;
use common\modules\reminder\widgets\ReminderFormWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ReminderForm */

$this->title = Yii::t('reminder', 'Update Reminder: {name}', [
	'name' => $model->getModel()->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('reminder', 'Reminders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->id, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('reminder', 'Update');
?>
<div class="reminder-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= ReminderFormWidget::widget([
		'model' => $model,
	]) ?>
</div>

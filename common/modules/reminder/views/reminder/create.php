<?php

use common\modules\reminder\models\ReminderForm;
use common\modules\reminder\widgets\ReminderFormWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ReminderForm */

$this->title = Yii::t('reminder', 'Create Reminder');
$this->params['breadcrumbs'][] = ['label' => Yii::t('reminder', 'Reminders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reminder-create">

	<h1><?= Html::encode($this->title) ?></h1>


	<?= ReminderFormWidget::widget([
		'model' => $model,
	]) ?>
</div>

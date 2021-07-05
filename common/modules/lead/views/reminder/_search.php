<?php

use common\modules\lead\models\searches\LeadReminderSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadReminderSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="lead-reminder-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">

		<?= $form->field($model, 'onlyToday', ['options' => ['class' => 'col-md-3']])->checkbox() ?>
		<?= $form->field($model, 'onlyDelayed', ['options' => ['class' => 'col-md-3']])->checkbox() ?>

	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>

		<?= Html::a(Yii::t('backend', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

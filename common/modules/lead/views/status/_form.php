<?php

use common\modules\calendar\widgets\FilterForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\LeadStatus */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-status-form">

	<?php $form = ActiveForm::begin([
		'id' => 'lead-status-form',
	]); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'short_report')->checkbox() ?>

	<?= $form->field($model, 'sort_index')->textInput() ?>

	<?= FilterForm::widget([
		'form' => $form,
		'model' => $model->getFilter(),
	]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

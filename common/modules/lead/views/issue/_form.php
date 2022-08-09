<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\LeadIssue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-issue-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'lead_id')->textInput() ?>

	<?= $form->field($model, 'issue_id')->textInput() ?>

	<?= $form->field($model, 'crm_id')->textInput() ?>

	<?= $form->field($model, 'created_at')->textInput() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

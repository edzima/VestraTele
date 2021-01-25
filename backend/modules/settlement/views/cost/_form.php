<?php

use backend\modules\settlement\models\IssueCostForm;
use common\widgets\DateWidget;
use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueCostForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-cost-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">

		<?= $model->getIssue() ? '' : $form->field($model, 'issue_id', ['options' => ['class' => 'col-md-2 col-lg-1']])->textInput() ?>

		<?= $form->field($model, 'type', ['options' => ['class' => 'col-md-4 col-lg-3']])->dropDownList(IssueCostForm::getTypesNames()) ?>

		<?= $form->field($model, 'date_at', ['options' => ['class' => 'col-md-4 col-lg-2']])->widget(DateWidget::class) ?>

	</div>


	<div class="row">
		<?= $form->field($model, 'value', ['options' => ['class' => 'col-xs-9 col-md-2 col-lg-3']])->widget(NumberControl::class) ?>

		<?= $form->field($model, 'vat', ['options' => ['class' => 'col-xs-3 col-md-2']])->widget(NumberControl::class) ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

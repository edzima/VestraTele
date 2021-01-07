<?php

use backend\modules\settlement\models\IssueCostForm;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueCostForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-cost-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $model->getIssue() ? '' : $form->field($model, 'issue_id')->textInput() ?>

	<?= $form->field($model, 'type')->dropDownList(IssueCostForm::getTypesNames()) ?>

	<?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'vat')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'date_at')->widget(DateTimeWidget::class, [
		'phpDatetimeFormat' => 'yyyy-MM-dd',
	]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

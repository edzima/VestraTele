<?php

use common\models\issue\IssuePay;
use common\models\settlement\PayPayedForm;
use common\modules\issue\widgets\IssueMessagesFormWidget;
use common\widgets\DateWidget;
use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PayPayedForm */
/* @var $form ActiveForm */
?>

<div class="issue-payed-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">

		<?= $form->field($model, 'value', ['options' => ['class' => 'col-md-2 col-lg-1']])->widget(NumberControl::class) ?>

		<?= $form->field($model, 'date', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'transfer_type', ['options' => ['class' => 'col-md-2']])
			->dropDownList(IssuePay::getTransfersTypesNames())
		?>

	</div>

	<div class="row">
		<div class="col-md-5 col-lg-3">
			<?= IssueMessagesFormWidget::widget([
				'form' => $form,
				'model' => $model->getMessagesModel(),
			]) ?>
		</div>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

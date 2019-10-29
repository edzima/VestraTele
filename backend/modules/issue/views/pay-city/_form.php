<?php

use backend\modules\address\widgets\AddressWidget;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssuePayCity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-pay-city-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= AddressWidget::widget([
		'form' => $form,
		'model' => $model->getAddress(),
		'subProvince' => false,
		'street' => false,
	]) ?>


	<div class="row">
		<?= $form->field($model, 'bank_transfer_at', ['options' => ['class' => 'col-md-6']])
			->widget(DateTimeWidget::class,
				[
					'phpDatetimeFormat' => 'yyyy-MM-dd',
					'clientOptions' => [

						'allowInputToggle' => true,
						'sideBySide' => true,
						'widgetPositioning' => [
							'horizontal' => 'auto',
							'vertical' => 'auto',
						],
					],
				]) ?>
		<?= $form->field($model, 'direct_at', ['options' => ['class' => 'col-md-6']])
			->widget(DateTimeWidget::class,
				[
					'phpDatetimeFormat' => 'yyyy-MM-dd',
					'clientOptions' => [

						'allowInputToggle' => true,
						'sideBySide' => true,
						'widgetPositioning' => [
							'horizontal' => 'auto',
							'vertical' => 'auto',
						],
					],
				]) ?>
	</div>

	<?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>


	<div class="form-group">
		<?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

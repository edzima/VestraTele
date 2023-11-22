<?php

use backend\helpers\Html;
use common\models\issue\IssueShipmentPocztaPolska;
use common\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var IssueShipmentPocztaPolska $model */
/** @var bool $withIssueField */
/** @var ActiveForm $form */
?>

<div class="issue-shipment-poczta-polska-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<?= $withIssueField
			? $form->field($model, 'issue_id', [
				'options' => [
					'class' => 'col-md-2 col-lg-1',
				],
			])->textInput()
			: ''
		?>

		<?= $form->field($model, 'shipment_number', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->textInput(['maxlength' => true]) ?>

	</div>
	<div class="row">
		<?= $form->field($model, 'details', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->textarea(['rows' => 2]) ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

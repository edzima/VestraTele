<?php

use backend\modules\settlement\models\SettlementTypeForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var SettlementTypeForm $model */
/** @var ActiveForm $form */
?>

<div class="settlement-type-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-md-4">
			<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

			<?= $form->field($model, 'is_active')->checkbox() ?>

			<?= $form->field($model, 'issueTypesIds')->widget(
				Select2::class, [
				'data' => $model->getIssueTypesNames(),
				'options' => [
					'multiple' => true,
				],
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('issueTypesIds'),
				],
			]) ?>


		</div>

		<div class="col-md-2">

			<?= $this->render('_options-form', [
				'form' => $form,
				'model' => $model->getOptions(),
			]) ?>
		</div>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('settlement', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

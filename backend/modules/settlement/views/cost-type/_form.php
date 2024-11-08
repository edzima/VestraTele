<?php

use backend\modules\settlement\models\CostTypeForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var CostTypeForm $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="cost-type-form">

	<?php $form = ActiveForm::begin([
		'id' => 'cost-type-form',
	]); ?>

	<div class="row">

		<div class="col-md-6">

			<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

			<?= $form->field($model, 'is_active')->checkbox() ?>

			<?= $form->field($model, 'is_for_settlement')->checkbox() ?>

			<?= $this->render('_options-form', [
				'form' => $form,
				'model' => $model->getOptions(),
			]) ?>


			<div class="form-group">
				<?= Html::submitButton(Yii::t('settlement', 'Save'), ['class' => 'btn btn-success']) ?>
			</div>
		</div>


	</div>

	<?php ActiveForm::end(); ?>

</div>

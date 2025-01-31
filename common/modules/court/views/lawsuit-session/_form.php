<?php

use common\modules\court\models\LawsuitSessionForm;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var LawsuitSessionForm $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="lawsuit-session-form">

	<?php $form = ActiveForm::begin(); ?>


	<div class="row">
		<div class="col-md-3 col-lg-2">
			<?= $form->field($model, 'date_at')->widget(DateTimeWidget::class) ?>
		</div>
		<div class="col-md-2 col-lg-1">
			<?= $form->field($model, 'room')->textInput(['maxlength' => true]) ?>
		</div>

		<div class="col-md-2 col-lg-1">
			<?= $form->field($model, 'location')
				->dropDownList(LawsuitSessionForm::getLocationNames(), [
					'prompt' => '---',
				]) ?>
		</div>

		<div class="col-md-2">

			<?= $form->field($model, 'presence_of_the_claimant')
				->dropDownList(LawsuitSessionForm::getPresenceOfTheClaimantNames(), [
					'prompt' => '---',
				]) ?>
		</div>

		<div class="col-md-2 col-lg-1">
			<?= !$model->getModel()->isNewRecord
				? $form->field($model, 'is_cancelled')->checkbox()
				: ''
			?>

		</div>

	</div>

	<div class="row">
		<div class="col-md-5 col-lg-4">
			<?= $form->field($model, 'url')->textInput() ?>
			<?= $form->field($model, 'details')->textarea(['rows' => 6]) ?>
		</div>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('court', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

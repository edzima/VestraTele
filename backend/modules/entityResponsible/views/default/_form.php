<?php

use backend\helpers\Html;
use backend\modules\entityResponsible\models\EntityResponsibleForm;
use common\widgets\ActiveForm;
use common\widgets\address\AddressFormWidget;

/* @var $this yii\web\View */
/* @var $model EntityResponsibleForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-entity-responsible-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-md-6">
			<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

			<?= $form->field($model, 'details')->textarea(['maxlength' => true, 'rows' => 7]) ?>

			<?= AddressFormWidget::widget([
				'model' => $model->getAddress(),
				'form' => $form,
			]) ?>

			<?= $form->field($model, 'is_for_summon')->checkbox() ?>

		</div>

	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

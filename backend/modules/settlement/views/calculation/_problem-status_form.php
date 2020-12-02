<?php

use backend\modules\settlement\models\CalculationProblemStatusForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model CalculationProblemStatusForm */
/* @var $form ActiveForm */
?>

<div class="settlement-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">

		<?= $form->field($model, 'status', ['options' => ['class' => 'col-md-3 col-lg-3']])->dropDownList(CalculationProblemStatusForm::getStatusesNames()) ?>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['id' => 'save-btn', 'class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>



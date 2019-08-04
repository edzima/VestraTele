<?php

use backend\modules\address\widgets\AddressWidget;
use common\models\Gmina;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model Gmina */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="state-form">

	<?php $form = ActiveForm::begin(); ?>


	<?= AddressWidget::widget([
		'form' => $form,
		'model' => $model,
		'state' => 'WOJ',
		'province' => 'POW',
	])
	?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

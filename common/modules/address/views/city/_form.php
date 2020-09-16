<?php

use common\modules\address\widgets\AddressFormWidget;
use common\models\address\City;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model City */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="city-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= AddressFormWidget::widget([
		'form' => $form,
		'model' => $model,
		'state' => 'wojewodztwo_id',
		'province' => 'powiat_id',
		'subProvince' => false,
		'street' => false,
		'cityAdd' => null,
		'city' => false,
	]) ?>

	<?= $form->field($model, 'name') ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

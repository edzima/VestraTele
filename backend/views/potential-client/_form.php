<?php

use common\helpers\Html;
use common\models\PotentialClient;
use common\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var PotentialClient $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="potential-client-form">

	<?php $form = ActiveForm::begin([
		'id' => 'form-potential-client',
	]); ?>

	<?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'details')->textarea(['rows' => 6]) ?>

	<?= $form->field($model, 'city_id')->textInput() ?>

	<?= $form->field($model, 'birthday')->textInput() ?>

	<?= $form->field($model, 'status')->textInput() ?>

	<?= $form->field($model, 'created_at')->textInput() ?>

	<?= $form->field($model, 'updated_at')->textInput() ?>

	<?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

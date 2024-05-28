<?php

use common\modules\file\models\File;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var File $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="file-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'hash')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'size')->textInput() ?>

	<?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'mime')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'file_type_id')->textInput() ?>

	<?= $form->field($model, 'owner_id')->textInput() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('file', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

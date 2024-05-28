<?php

use common\modules\file\models\FileTypeForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var FileTypeForm $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="file-type-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'is_active')->checkbox() ?>

	<?= $form->field($model, 'visibility')->dropDownList(FileTypeForm::getVisibilityNames()) ?>

	<?= $this->render('_validator-form', [
		'model' => $model->getOptions(),
		'form' => $form,
	]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('file', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

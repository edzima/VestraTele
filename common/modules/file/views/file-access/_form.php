<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\modules\file\models\FileAccess $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="file-access-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'file_id')->textInput() ?>

	<?= $form->field($model, 'user_id')->textInput() ?>

	<?= $form->field($model, 'access')->textInput(['maxlength' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('file', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

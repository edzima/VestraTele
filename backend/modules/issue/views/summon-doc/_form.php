<?php

use common\models\issue\SummonDoc;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model SummonDoc */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="summon-doc-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'priority')->dropDownList(SummonDoc::getPriorityNames(), [
		'prompt' => Yii::t('common', '--- Select ---'),
	]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

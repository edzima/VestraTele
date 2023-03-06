<?php

use backend\modules\issue\models\SummonDocForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model SummonDocForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="summon-doc-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'priority')->dropDownList(SummonDocForm::getPriorityNames(), [
		'prompt' => Yii::t('common', '--- Select ---'),
	]) ?>

	<?= $form->field($model, 'summonTypesIds')->widget(Select2::class, [
			'data' => SummonDocForm::getSummonTypesNames(),
			'options' => ['multiple' => true, 'placeholder' => $model->getAttributeLabel('summonTypesIds')]
		]
	) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

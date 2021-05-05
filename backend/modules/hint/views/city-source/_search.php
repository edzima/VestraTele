<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\hint\searches\HintCitySourceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hint-city-source-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'source_id') ?>

	<?= $form->field($model, 'hint_id') ?>

	<?= $form->field($model, 'phone') ?>

	<?= $form->field($model, 'rating') ?>

	<?= $form->field($model, 'details') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('hint', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('hint', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

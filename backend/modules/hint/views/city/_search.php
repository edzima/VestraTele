<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\hint\searches\HintCitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hint-city-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'user_id') ?>

	<?= $form->field($model, 'city_id') ?>

	<?= $form->field($model, 'type') ?>

	<?= $form->field($model, 'status') ?>

	<?php // echo $form->field($model, 'details') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('hint', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('hint', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

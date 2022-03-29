<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\issue\models\search\SummonDocSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="summon-doc-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'name') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

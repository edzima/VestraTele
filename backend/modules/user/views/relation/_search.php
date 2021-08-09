<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\user\models\search\RelationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-relation-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'user_id') ?>

	<?= $form->field($model, 'to_user_id') ?>

	<?= $form->field($model, 'type') ?>

	<?= $form->field($model, 'created_at') ?>

	<?= $form->field($model, 'updated_at') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

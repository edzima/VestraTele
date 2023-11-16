<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\modules\file\models\search\FileSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="file-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'name') ?>

	<?= $form->field($model, 'hash') ?>

	<?= $form->field($model, 'size') ?>

	<?= $form->field($model, 'type') ?>

	<?php // echo $form->field($model, 'mime') ?>

	<?php // echo $form->field($model, 'file_type_id') ?>

	<?php // echo $form->field($model, 'created_at') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<?php // echo $form->field($model, 'owner_id') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('file', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('file', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

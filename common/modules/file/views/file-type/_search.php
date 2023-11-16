<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\modules\file\models\search\FileTypeSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="file-type-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'name') ?>

	<?= $form->field($model, 'is_active') ?>

	<?= $form->field($model, 'visibility') ?>

	<?= $form->field($model, 'validator_config') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('file', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('file', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

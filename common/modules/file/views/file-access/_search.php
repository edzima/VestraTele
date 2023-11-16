<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\modules\file\models\search\FileAccessSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="file-access-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'file_id') ?>

	<?= $form->field($model, 'user_id') ?>

	<?= $form->field($model, 'access') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('file', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('file', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

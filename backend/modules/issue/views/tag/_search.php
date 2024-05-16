<?php

use backend\modules\issue\models\search\TagSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TagSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-tag-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'name') ?>

	<?= $form->field($model, 'description') ?>

	<?= $form->field($model, 'is_active') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('issue', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('issue', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

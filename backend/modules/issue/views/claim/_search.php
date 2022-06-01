<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\issue\models\search\ClaimSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-claim-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'issue_id') ?>

	<?= $form->field($model, 'type') ?>

	<?= $form->field($model, 'trying_value') ?>

	<?= $form->field($model, 'obtained_value') ?>

	<?php // echo $form->field($model, 'is_percent') ?>

	<?php // echo $form->field($model, 'details') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('issue', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('issue', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

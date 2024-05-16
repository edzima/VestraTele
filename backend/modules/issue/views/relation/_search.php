<?php

use backend\modules\issue\models\search\RelationSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model RelationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-relation-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'issue_id_1') ?>

	<?= $form->field($model, 'issue_id_2') ?>

	<?= $form->field($model, 'created_at') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('issue', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('issue', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

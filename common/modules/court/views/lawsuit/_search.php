<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\modules\court\models\search\LawsuitSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="court-hearing-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'issue_id') ?>

	<?= $form->field($model, 'court_id') ?>

	<?= $form->field($model, 'signature_act') ?>

	<?= $form->field($model, 'room') ?>

	<?php // echo $form->field($model, 'due_at') ?>

	<?php // echo $form->field($model, 'created_at') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<?php // echo $form->field($model, 'creator_id') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('court', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('court', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

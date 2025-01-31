<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\modules\court\models\search\LawsuitSessionSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="lawsuit-session-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'details') ?>

	<?= $form->field($model, 'lawsuit_id') ?>

	<?= $form->field($model, 'date_at') ?>

	<?= $form->field($model, 'created_at') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<?php // echo $form->field($model, 'room') ?>

	<?php // echo $form->field($model, 'is_cancelled') ?>

	<?php // echo $form->field($model, 'presence_of_the_claimant') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('court', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('court', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

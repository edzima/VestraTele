<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\modules\court\models\search\CourtSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="court-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'name') ?>

	<?= $form->field($model, 'address') ?>

	<?= $form->field($model, 'type') ?>

	<?= $form->field($model, 'phone') ?>

	<?php // echo $form->field($model, 'fax') ?>

	<?php // echo $form->field($model, 'email') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<?php // echo $form->field($model, 'parent_id') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('court', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('court', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\settlement\search\SettlementTypeSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="settlement-type-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'name') ?>

	<?= $form->field($model, 'is_active') ?>

	<?= $form->field($model, 'issue_types') ?>

	<?= $form->field($model, 'options') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('settlement', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('settlement', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

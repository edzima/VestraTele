<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\modules\issue\models\search\ShipmentPocztaPolskaSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="issue-shipment-poczta-polska-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'issue_id') ?>

	<?= $form->field($model, 'shipment_number') ?>

	<?= $form->field($model, 'created_at') ?>

	<?= $form->field($model, 'updated_at') ?>

	<?= $form->field($model, 'shipment_at') ?>

	<?php // echo $form->field($model, 'finished_at') ?>

	<?php // echo $form->field($model, 'apiData') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('issue', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('issue', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

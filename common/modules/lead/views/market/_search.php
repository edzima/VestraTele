<?php

use common\modules\lead\models\searches\LeadMarketSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadMarketSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-market-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'lead_id') ?>

	<?= $form->field($model, 'status') ?>

	<?= $form->field($model, 'created_at') ?>

	<?= $form->field($model, 'updated_at') ?>

	<?php // echo $form->field($model, 'options') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('lead', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

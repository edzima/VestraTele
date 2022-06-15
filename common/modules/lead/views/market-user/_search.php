<?php

use common\modules\lead\models\searches\LeadMarketUserSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadMarketUserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-market-user-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'market_id') ?>

	<?= $form->field($model, 'lead_id') ?>

	<?= $form->field($model, 'status') ?>

	<?= $form->field($model, 'created_at') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('lead', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

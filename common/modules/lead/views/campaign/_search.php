<?php

use common\modules\lead\models\searches\LeadCampaignSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadCampaignSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-source-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'entity_id', [
			'options' => [
				'class' => 'col-md-2',
			],
		]) ?>

		<?= $form->field($model, 'details', [
			'options' => [
				'class' => 'col-md-4',
			],
		]) ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('lead', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

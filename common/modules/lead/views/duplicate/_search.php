<?php

use common\modules\lead\models\searches\DuplicateLeadSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model DuplicateLeadSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-report-schema-search">

	<?php $form = ActiveForm::begin([
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'status')->dropDownList(
		DuplicateLeadSearch::getStatusFilterNames(), [
		'prompt' => Yii::t('lead', 'Select...'),
	]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

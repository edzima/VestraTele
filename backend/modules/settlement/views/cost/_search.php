<?php

use backend\helpers\Html;
use backend\modules\settlement\models\search\IssueCostSearch;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueCostSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pay-received-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'settled', ['options' => ['class' => 'col-xs-6']])->dropDownList(
			Html::booleanDropdownList(), [
			'prompt' => Yii::t('common', 'All'),
		])
		?>

		<?= $form->field($model, 'withSettlements', ['options' => ['class' => 'col-xs-6']])->dropDownList(
			Html::booleanDropdownList(), [
			'prompt' => Yii::t('common', 'All'),
		])
		?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('settlement', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('settlement', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

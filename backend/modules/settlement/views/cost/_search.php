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
		<?= $form->field($model, 'settled', ['options' => ['class' => 'col-xs-4']])->dropDownList(
			Html::booleanDropdownList(), [
			'prompt' => Yii::t('common', 'All'),
		])
		?>

		<?= $form->field($model, 'withSettlements', ['options' => ['class' => 'col-xs-4']])->dropDownList(
			Html::booleanDropdownList(), [
			'prompt' => Yii::t('common', 'All'),
		])
		?>

		<?= $form->field($model, 'is_confirmed', ['options' => ['class' => 'col-xs-4']])->dropDownList(
			Html::booleanDropdownList(), [
			'prompt' => Yii::t('common', 'All'),
		])
		?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('common', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

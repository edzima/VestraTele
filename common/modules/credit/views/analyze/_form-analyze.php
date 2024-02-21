<?php

use common\helpers\Html;
use common\modules\credit\models\CreditClientAnalyze;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model CreditClientAnalyze */

?>

<div class="credit-analyze-form">
	<?php $form = ActiveForm::begin([
		//	'method' => 'GET',
	]) ?>

	<div class="row">
		<?= $form->field($model, 'borrower', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->textInput() ?>

		<?= $form->field($model, 'entityResponsibleId', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->widget(Select2::class, [
			'data' => $model->getEntityResponsibleNames(),
		]) ?>


	</div>

	<div class="row">
		<?= $form->field($model, 'agreement', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->textInput() ?>

		<?= $form->field($model, 'analyzeResult', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->textarea() ?>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('issue', 'PDF'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('common', 'Reset'), ['calc'], ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>
</div>

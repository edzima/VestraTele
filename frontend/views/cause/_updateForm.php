<?php

use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Cause */
/* @var $form yii\widgets\ActiveForm */

?>

<p class="pull-right">
	<?= Html::a(Yii::t('frontend', 'Delete'), ['delete', 'id' => $model->id], [
		'class' => 'btn btn-danger',
		'data' => [
			'confirm' => Yii::t('frontend', 'Are you sure you want to delete this item?'),
			'method' => 'post',
		],
	]) ?>
</p>


<div class="cause-form">
	<div class="title-block clearfix">
		<h3 class="h3-body-title">Dane</h3>
		<div class="title-separator"></div>
	</div>
	<?php $form = ActiveForm::begin([
		'options' => [
			'id' => 'cause-form',
		],
	]); ?>

	<?= $form->field($model, 'victim_name',
		[
			'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-blind fa-lg"></i> Poszkodowany</span>{input}</div>',
		])
		->textInput(['maxlength' => true]) ?>


	<?= $form->field($model, 'category_id',
		[
			'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-line-chart"></i> Etap</span>{input}</div>',

		])
		->dropDownList($category) ?>


	<?= $form->field($model, 'date',
		[
			'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i> Start etapu</span>{input}</div>',

		])
		->widget(DateTimeWidget::class,
			[
				'clientOptions' => [

					'allowInputToggle' => true,
					'sideBySide' => true,

					'widgetPositioning' => [
						'horizontal' => 'auto',
						'vertical' => 'auto',
					],
				],
			])
	?>


	<?= $form->field($model, 'is_finished')->checkBox(['label' => 'Archiwum', 'selected' => $model->is_finished]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>


	<?php ActiveForm::end(); ?>

</div>






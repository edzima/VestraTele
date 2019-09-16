<?php

use common\widgets\DateTimeWidget;
use frontend\models\BenefitAmountAlignmentForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model BenefitAmountAlignmentForm */
?>

<div class="benefit-amount-form">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">


		<?= $form->field($model, 'benefitFromAt', ['options' => ['class' => 'col-md-4']])
			->widget(DateTimeWidget::class,
				[
					'phpDatetimeFormat' => 'yyyy-MM-dd',
					'clientOptions' => [
						'allowInputToggle' => true,
						'sideBySide' => true,
						'viewMode' => 'months',
						'widgetPositioning' => [
							'horizontal' => 'auto',
							'vertical' => 'auto',
						],
					],
				])->hint('Zostaw puste, gdy powyżej 3 lat.') ?>

		<?= $form->field($model, 'benefitToAt', ['options' => ['class' => 'col-md-4']])
			->widget(DateTimeWidget::class,
				[
					'phpDatetimeFormat' => 'yyyy-MM-dd',
					'clientOptions' => [
						'viewMode' => 'months',

						'allowInputToggle' => true,
						'sideBySide' => true,
						'widgetPositioning' => [
							'horizontal' => 'auto',
							'vertical' => 'auto',
						],
					],
				]) ?>


		<?= $form->field($model, 'lawsuitAt', ['options' => ['class' => 'col-md-4']])
			->widget(DateTimeWidget::class,
				[
					'phpDatetimeFormat' => 'yyyy-MM-dd',
					'clientOptions' => [
						'viewMode' => 'months',
						'allowInputToggle' => true,
						'sideBySide' => true,
						'widgetPositioning' => [
							'horizontal' => 'auto',
							'vertical' => 'auto',
						],
					],
				]) ?>


	</div>


	<div class="form-group">
		<?= Html::submitButton('Oblicz', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>


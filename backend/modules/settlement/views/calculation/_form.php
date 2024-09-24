<?php

use backend\modules\settlement\models\CalculationForm;
use common\modules\issue\widgets\IssueMessagesFormWidget;
use common\widgets\ActiveForm;
use common\widgets\DateWidget;
use kartik\number\NumberControl;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model CalculationForm */
/* @var $form ActiveForm */

$providerTypeId = Html::getInputId($model, 'providerType');
$providerEntity = CalculationForm::PROVIDER_TYPE_RESPONSIBLE_ENTITY;
$js = <<<JS
 const providerTypeInput = document.getElementById('$providerTypeId');
 const entityResponsibleField = document.getElementById('entity-provider-field');
 const providerEntity = $providerEntity;
 providerTypeInput.addEventListener('change',(event)=>{
		if(parseInt(event.target.value) === providerEntity){
			entityResponsibleField.classList.remove('hidden');
		}else{
			entityResponsibleField.classList.add('hidden');
		}
 });
JS;

$this->registerJs($js);
?>

<div class="settlement-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">

		<?= $form->field($model, 'providerType', [
			'options' => ['class' => 'col-md-3 col-lg-2'],
		])->dropDownList($model->getProvidersNames()) ?>

		<?= $form->field($model, 'entityProviderId', [
			'options' => [
				'id' => 'entity-provider-field',
				'class' => 'col-md-4 col-lg-3' . ($model->providerType !== CalculationForm::PROVIDER_TYPE_RESPONSIBLE_ENTITY ? ' hidden' : ''),
			],
		])->widget(Select2::class, [
				'data' => CalculationForm::getEntityResponsibleNames(),
			]
		) ?>

	</div>
	<div class="row">
		<?= $form->field($model, 'value', ['options' => ['class' => 'col-xs-9 col-md-2 col-lg-2']])->widget(NumberControl::class) ?>

		<?= $model->getModel()->isNewRecord
			? $form->field($model, 'vat', ['options' => ['class' => 'col-xs-3 col-md-1']])->widget(NumberControl::class)
			: '' ?>

	</div>


	<div class="row">
		<?= $model->getModel()->isNewRecord || $model->getModel()->getPaysCount() < 2
			? $form->field($model, 'payment_at', ['options' => ['class' => 'col-md-3 col-lg-2']])
				->widget(DateWidget::class)
			. $form->field($model, 'deadline_at', ['options' => ['class' => 'col-md-3 col-lg-2']])
				->widget(DateWidget::class)
			: ''
		?>
	</div>

	<div class="row">
		<?= $form->field($model, 'costs_ids', ['options' => ['class' => 'col-md-6 col-lg-4']])
			->widget(Select2::class, [
					'data' => $model->getCostsData(),
					'options' => [
						'multiple' => true,
						'placeholder' => $model->getAttributeLabel('costs_ids'),
					],
				]
			)
		?>
	</div>


	<?php if ($model->getMessagesModel()): ?>
		<div class="row">
			<div class="col-md-4">
				<?= IssueMessagesFormWidget::widget([
					'form' => $form,
					'model' => $model->getMessagesModel(),
				]) ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['id' => 'save-btn', 'class' => 'btn btn-success']) ?>
	</div>


	<?php ActiveForm::end(); ?>

</div>



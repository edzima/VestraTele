<?php

use common\helpers\Html;
use common\helpers\Url;
use common\models\issue\form\SummonForm;
use common\models\user\Worker;
use common\widgets\ActiveForm;
use common\widgets\address\CitySimcInputWidget;
use common\widgets\DateTimeWidget;
use common\widgets\DateWidget;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model SummonForm */

?>
	<div class="summon-form">

		<?php $form = ActiveForm::begin([
			'id' => 'summon-form',
		]); ?>

		<div class="row">

			<?= !empty($model->issuesIds)
				? Html::hiddenInput('issuesIds', implode(',', $model->issuesIds))
				: ''
			?>
			<?= $form->field($model, 'doc_types_ids', [
				'options' => [
					'class' => 'col-md-12',
				],
			])
				->widget(DepDrop::class, [
					'type' => DepDrop::TYPE_SELECT2,
					'data' => $model->getDocNames(),
					'pluginOptions' => [
						'depends' => [Html::getInputId($model, 'type_id')],
						'url' => Url::to(['summon-doc/types-list']),
						'loading' => Yii::t('common', 'Loading...'),
					],
					'options' => [
						'placeholder' => $model->getAttributeLabel('doc_types_ids'),
						'allowClear' => true,
						'multiple' => true,
					],
					'disabled' => !(
						(
							Yii::$app->user->can(Worker::PERMISSION_SUMMON_DOC_MANAGER)
							|| Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
						)
						|| (
							!$model->getModel()->isNewRecord && $model->getModel()->owner_id === Yii::$app->user->getId()
						)
					),
				])
			?>
		</div>

		<div class="row">
			<?= $form->field($model, 'title', [
				'options' => [
					'class' => 'col-md-7',
				],
			])
				->textarea(['maxlength' => true]) ?>
		</div>

		<div class="row">

			<?= $form->field($model, 'type_id', [
				'options' => [
					'class' => 'col-md-2',
				],
			])->widget(Select2::class, [
					'data' => SummonForm::getTypesNames(),
				]
			) ?>



			<?= !$model->getModel()->isNewRecord
				? $form->field($model, 'status', [
					'options' => [
						'class' => 'col-md-3',
					],
				])->dropDownList(SummonForm::getStatusesNames())
				: ''
			?>

			<?= $model->getModel()->isNewRecord
				? $form->field($model, 'term', [
					'options' => [
						'class' => 'col-md-2',
					],
				])->dropDownList(SummonForm::getTermsNames())
				: ''
			?>


			<?= $form->field($model, 'deadline_at', [
				'options' => [
					'id' => 'deadline_at_field',
					'class' => 'col-md-2' . ($model->getModel()->isNewRecord && $model->term !== SummonForm::TERM_CUSTOM ? ' hidden' : ''),
				],
			])
				->widget(DateWidget::class) ?>


		</div>

		<div class="row">

			<?= $form->field($model, 'contractor_id',
				['options' => ['class' => 'col-md-3 col-lg-2']])
				->widget(Select2::class, [
						'data' => $model->getContractors(),
						'pluginOptions' => [
							'placeholder' => $model->getAttributeLabel('contractor_id'),
						],
					]
				)
			?>

			<?= $form->field($model, 'entity_id', [
				'options' => [
					'class' => 'col-md-3 col-lg-2',
				],
			])->widget(Select2::class, [
				'data' => SummonForm::getEntityNames(),
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('entity_id'),
				],
			])
			?>

			<?= $form->field($model, 'city_id', [
				'options' => [
					'class' => 'col-md-4 col-lg-3',
				],
			])->widget(CitySimcInputWidget::class) ?>
		</div>


		<div class="row">

			<?= $form->field($model, 'start_at', [
				'options' => [
					'class' => 'col-md-2',
				],
			])
				->widget(DateWidget::class) ?>

			<?= $form->field($model, 'realize_at', [
				'options' => [
					'class' => 'col-md-2',
				],
			])->hint(Yii::t('issue', 'Used for the Calendar.'))
				->widget(DateTimeWidget::class) ?>


			<?= !$model->getModel()->isNewRecord
				? $form->field($model, 'realized_at', [
					'options' => [
						'class' => 'col-md-2',
					],
				])
					->widget(DateTimeWidget::class)
				: ''
			?>

		</div>
		<div class="row">
			<?= $model->getModel()->isNewRecord
				? $form->field($model, 'sendEmailToContractor', [
					'options' => [
						'class' => 'col-md-2',
					],
				])->checkbox()
				: ''
			?>
		</div>


		<div class="form-group">
			<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>


<?php

$termInputId = Html::getInputId($model, 'term');
$termCustomValue = SummonForm::TERM_CUSTOM;
$js = <<<JS

const termInput = document.getElementById('$termInputId');
const deadlineAtField = document.getElementById('deadline_at_field');


if(termInput){
	termInput.onchange= function(){
	if(this.value === '$termCustomValue'){
		deadlineAtField.classList.remove('hidden');
	}else{
		deadlineAtField.classList.add('hidden');
	}
};
}

JS;

$this->registerJs($js);

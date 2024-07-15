<?php

use common\helpers\Html;
use common\modules\lead\models\LeadCSVImport;
use common\widgets\ActiveForm;
use kartik\number\NumberControl;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model LeadCSVImport */

$this->title = Yii::t('lead', 'Import Leads from CSV');

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="lead-import-csv">

	<?php $form = ActiveForm::begin([
		'id' => 'lead-import-csv-form',
		'options' => ['enctype' => 'multipart/form-data'],
	]); ?>

	<?= $form->field($model, 'csvFile')->fileInput([
		'accept' => '.csv',
	]) ?>

	<div class="row">

		<?= $form->field($model, 'status_id', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])
			->widget(Select2::class, [
					'data' => LeadCSVImport::getStatusNames(),
					'options' => [
						'placeholder' => $model->getAttributeLabel('status_id'),
					],
				]
			)
		?>

		<?= $form->field($model, 'source_id', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])
			->widget(Select2::class, [
					'data' => LeadCSVImport::getSourcesNames(),
					'options' => [
						'placeholder' => $model->getAttributeLabel('source_id'),
					],
				]
			)
		?>

		<?= $form->field($model, 'provider', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])
			->widget(Select2::class, [
					'data' => LeadCSVImport::getProvidersNames(),
					'options' => [
						'placeholder' => $model->getAttributeLabel('source_id'),
					],
				]
			)
		?>

	</div>

	<div class="row">
		<?= $form->field($model, 'phoneColumn', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->widget(NumberControl::class, [
			'maskedInputOptions' => [
				'digits' => 0,
			],
		])
			->hint(Yii::t('csv', 'Column'))
		?>

		<?= $form->field($model, 'nameColumn', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->widget(NumberControl::class, [
			'maskedInputOptions' => [
				'digits' => 0,
			],
		])
			->hint(Yii::t('csv', 'Column - Leave empty for Generate Name from Filename'))
		?>

		<?= $form->field($model, 'dateColumn', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->widget(NumberControl::class, [
			'maskedInputOptions' => [
				'digits' => 0,
			],
		])
			->hint(Yii::t('csv', 'Column - Leave empty for Current Date'))
		?>
	</div>

	<div class="row">
		<?= $form->field($model, 'csvDelimiter', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->dropDownList(LeadCSVImport::delimiters()) ?>

		<?= $form->field($model, 'startFromLine', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->widget(NumberControl::class, [
			'maskedInputOptions' => [
				'digits' => 0,
			],
		])->hint(Yii::t('csv', 'Row')) ?>

	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Import'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>



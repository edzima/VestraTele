<?php

use backend\helpers\Html;
use common\modules\credit\components\InterestRate;
use common\modules\credit\models\CreditSanctionCalc;
use common\widgets\ActiveForm;
use common\widgets\DateWidget;
use kartik\number\NumberControl;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model CreditSanctionCalc */
/* @var $form yii\widgets\ActiveForm */

$inputNameTnterestRateType = Html::getInputName($model, 'interestRateType');
$interestRateTypeId = Html::getInputId($model, 'interestRateType');
$interestRatePercentInputId = Html::getInputId($model, 'interestRatePercent');
$interestRatePercentNames = Json::encode(InterestRate::interestRatePercentNames());
$js = <<<JS
		const interestRateTypeInput = document.getElementById('$interestRateTypeId');
		const interestRatePercentLabel = document.querySelector('label[for="$interestRatePercentInputId"]');
		const interestRatePercenetLablesNames = $interestRatePercentNames;
		interestRateTypeInput.onchange = function (){
			const val = document.querySelector('input[name="$inputNameTnterestRateType"]:checked').value;
			const label = interestRatePercenetLablesNames[val];
			if(label){
				interestRatePercentLabel.innerText = label;
			}
		}
JS;

$this->registerJs($js);
?>

<div class="credit-calc-form">

	<?php $form = ActiveForm::begin([
		'action' => ['calc'],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'sumCredit', [
			'options' => [
				'class' => [
					'col-md-3 col-lg-2',
				],
			],
		])
			->widget(NumberControl::class)
		?>

		<?= $form->field($model, 'provision', [
			'options' => [
				'class' => [
					'col-md-2 col-lg-1',
				],
			],
		])
			->widget(NumberControl::class)
		?>


	</div>

	<div class="row">


		<?= $form->field($model, 'periods', [
			'options' => [
				'class' => [
					'col-md-2 col-lg-1',
				],
			],
		])
			->widget(NumberControl::class, [
				'displayOptions' => ['placeholder' => Yii::t('credit', 'Periods count')],
			])

		?>


		<?= $form->field($model, 'installmentsType', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->radioList(CreditSanctionCalc::getInstallmentsTypes())->label(false) ?>


		<?= $form->field($model, 'interestRateType', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->radioList(CreditSanctionCalc::getInterestRateNames())->label(false) ?>


		<?= $form->field($model, 'interestRatePercent', [
			'inputTemplate' => '<div class="input-group">'
				. '{input}'
				. '<span class="input-group-addon"><i class="fa fa-percent"></i></span></div>',
			'options' => [
				'class' => [
					'col-md-2 col-lg-1',
				],
				'placeholder' => 'prowizja',
			],
		])
			->widget(NumberControl::class)
		?>
	</div>

	<div class="row">

		<?= $form->field($model, 'firstInstallmentAt', [
			'options' => [
				'class' => [
					'col-md-3 col-lg-2',
				],
			],
		])
			->widget(DateWidget::class, [
				'clientOptions' => [
					'viewMode' => 'years',
					'allowInputToggle' => true,
					'sideBySide' => true,
					'widgetPositioning' => [
						'horizontal' => 'auto',
						'vertical' => 'auto',
					],
				],
			])
		?>



		<?= $form->field($model, 'dateAt', [
			'options' => [
				'class' => [
					'col-md-3 col-lg-2',
				],
			],
		])
			->widget(DateWidget::class)
		?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('credit', 'Calc'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('credit', 'Reset'), ['calc'], ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

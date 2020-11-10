<?php

use backend\modules\issue\models\MeetForm;
use common\widgets\address\AddressFormWidget;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model MeetForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-meet-form">

	<?php $form = ActiveForm::begin(); ?>


	<div class="row">
		<?= $form->field($model, 'agentId', ['options' => ['class' => 'col-md-3']])
			->widget(Select2::class, [
					'data' => MeetForm::getAgentsNames(),
					'options' => [
						'placeholder' => 'Agent',
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>
	</div>

	<div class="row">
		<?= $form->field($model, 'createdAt', [
			'options' => [
				'class' => 'col-md-3',
			],
		])
			->widget(DateTimeWidget::class)
		?>

		<?= $form->field($model, 'dateStart', [
			'options' => [
				'class' => 'col-md-3',
			],
		])
			->widget(DateTimeWidget::class)
		?>
	</div>
	<div class="row">
		<?= $form->field($model, 'campaignId', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->dropDownList(MeetForm::getCampaignNames()) ?>

		<?= $form->field($model, 'typeId', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->dropDownList(MeetForm::getTypesNames()) ?>

		<?= $form->field($model, 'status', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->dropDownList(MeetForm::getStatusNames()) ?>
	</div>


	<fieldset>
		<legend>Klient</legend>
		<div class="row">
			<?= $form->field($model, 'clientName', [
				'options' => [
					'class' => 'col-md-3',
				],
			])->textInput([
			]) ?>

			<?= $form->field($model, 'clientSurname', [
				'options' => [
					'class' => 'col-md-3',
				],
			])->textInput([
			]) ?>

			<?= $form->field($model, 'phone', [
				'options' => [
					'class' => 'col-md-3',
				],
			])->textInput(['maxlength' => true]) ?>

			<?= $form->field($model, 'email', [
				'options' => [
					'class' => 'col-md-3',
				],
			])->textInput(['maxlength' => true]) ?>

		</div>
		<?= $form->field($model, 'withAddress')->checkbox() ?>
		<div id="address-wrapper" class="address-wrapper<?= !$model->withAddress ? ' hidden' : '' ?>">
			<?= AddressFormWidget::widget([
				'form' => $form,
				'model' => $model->getAddress(),
			]) ?>
		</div>

	</fieldset>


	<?= $form->field($model, 'details')->textarea(['rows' => 6]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

<?php

$withAddressID = Html::getInputId($model, 'withAddress');

$js = <<<JS
	const withAddress = document.getElementById('$withAddressID');
	const addressWrapper = document.getElementById('address-wrapper');
			withAddress.addEventListener('change',function (){
		if(withAddress.checked){
			addressWrapper.classList.remove('hidden');
		}else{
			addressWrapper.classList.add('hidden');
		}
	});
JS;

$this->registerJs($js);

?>

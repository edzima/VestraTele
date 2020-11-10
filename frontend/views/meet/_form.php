<?php

use common\widgets\address\AddressFormWidget;
use common\widgets\DateTimeWidget;
use frontend\models\meet\MeetForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model MeetForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-meet-form">

	<?php $form = ActiveForm::begin(); ?>



	<?= $form->field($model, 'typeId')->dropDownList(MeetForm::getTypesNames()) ?>

	<?= $form->field($model, 'status')->dropDownList(MeetForm::getStatusNames()) ?>

	<?= $form->field($model, 'details')->textarea(['rows' => 6]) ?>


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

		<?= AddressFormWidget::widget([
			'form' => $form,
			'model' => $model->getAddress(),
		]) ?>
	</fieldset>

	<div class="row">


		<?= $form->field($model, 'dateStart', [
			'options' => [
				'class' => 'col-md-6',
			],
		])
			->widget(DateTimeWidget::class) ?>

		<?= $form->field($model, 'dateEnd')
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
				]) ?>

	</div>


	<div class="form-group">
		<?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
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

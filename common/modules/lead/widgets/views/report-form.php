<?php

use common\helpers\Html;
use common\modules\lead\models\forms\ReportForm;
use common\widgets\address\AddressFormWidget;
use kartik\select2\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $form ActiveForm|null */
/* @var $formOptions array */
/* @var $model ReportForm */
/* @var $withSameContacts bool */
/* @var $withName bool */

?>

<div class="lead-report-form-fields">

	<div class="row">

		<?= $form->field($model, 'status_id', ['options' => ['class' => 'col-md-5 col-lg-4']])
			->widget(Select2::class, ['data' => ReportForm::getStatusNames()])
		?>

		<?= $withName
			? $form->field($model, 'leadName', ['options' => ['class' => 'col-md-6 col-lg-5']])->textInput()
			: ''
		?>

		<?= $form->field($model, 'withAddress', ['options' => ['class' => 'col-md-1']])->checkbox() ?>

		<?= $form->field($model, 'is_pinned', ['options' => ['class' => 'col-md-1']])->checkbox() ?>

	</div>

	<div id="address-wrapper" class="address-wrapper<?= !$model->withAddress ? ' hidden' : '' ?>">
		<?= AddressFormWidget::widget([
			'form' => $form,
			'model' => $model->getAddress(),
		]) ?>
	</div>


	<div class="row">
		<?= $form->field($model, 'tele_id', [
			'options' => [
				'class' => 'col-md-5 col-lg-4',
			],

		])->widget(
			Select2::class, [
				'data' => $model->getTeleUsersNames(),
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('tele_id'),
				],
			]

		) ?>

		<?= $form->field($model, 'partner_id', [
			'options' => [
				'class' => 'col-md-5 col-lg-4',
			],
		])->widget(
			Select2::class, [
				'data' => $model->getUsersNames(),
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('partner_id'),
				],
			]
		) ?>
	</div>
	<?= $form->field($model, 'details')->textarea() ?>


	<?= $withSameContacts
		? $form->field($model, 'withSameContacts')->checkbox()
		: ''
	?>


	<?= $this->render('_answers-report-form', [
		'model' => $model->getAnswersModel(),
		'form' => $form,
	]) ?>


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


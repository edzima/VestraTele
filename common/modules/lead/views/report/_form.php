<?php

use common\helpers\Html;
use common\modules\lead\models\forms\ReportForm;
use common\widgets\ActiveForm;
use common\widgets\address\AddressFormWidget;
use kartik\select2\Select2;

/* @var $this \yii\web\View */
/* @var $model ReportForm */

?>


<div class="lead-report-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'status_id')->dropDownList(ReportForm::getStatusNames()) ?>

	<?= $form->field($model, 'withAddress')->checkbox() ?>
	<div id="address-wrapper" class="address-wrapper<?= !$model->withAddress ? ' hidden' : '' ?>">
		<?= AddressFormWidget::widget([
			'form' => $form,
			'model' => $model->getAddress(),
		]) ?>
	</div>


	<?php foreach ($model->getAnswersModels() as $id => $answer): ?>

		<?= $form->field($answer, "[$id]answer")
			->textInput(['placeholder' => $answer->getQuestion()->placeholder])
			->label($answer->getQuestion()->name)
		?>

	<?php endforeach; ?>



	<?= $form->field($model, 'details')->textarea() ?>

	<?= $form->field($model, 'closedQuestions')->widget(Select2::class, [
		'data' => $model->getClosedQuestionsData(),
		'options' => [
			'multiple' => true,
		],
	])
	?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
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


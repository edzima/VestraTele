<?php

use common\helpers\Html;
use common\modules\lead\models\forms\ReportForm;
use common\widgets\address\AddressFormWidget;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $form ActiveForm|null */
/* @var $formOptions array */
/* @var $model ReportForm */

?>

<div class="lead-report-form-fields">

	<?= $form->field($model, 'status_id')
		->widget(Select2::class, ['data' => ReportForm::getStatusNames()]) ?>

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




	<?= !empty($model->getClosedQuestionsData())
		? $form->field($model, 'closedQuestions')->widget(Select2::class, [
			'data' => $model->getClosedQuestionsData(),
			'options' => [
				'multiple' => true,
			],
		])
		: ''
	?>


	<?= $form->field($model, 'details')->textarea() ?>


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


<?php

use backend\modules\user\models\CustomerUserForm;
use backend\modules\user\widgets\UserProfileFormWidget;
use common\widgets\address\AddressFormWidget;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var $model CustomerUserForm */

?>


<?php $form = ActiveForm::begin(['id' => 'customer-form']) ?>

<div class="row">


	<?= $model->scenario === CustomerUserForm::SCENARIO_CREATE
	&& $model->hasDuplicates()
		? $form->field($model, 'acceptDuplicates', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->checkbox()
		: ''
	?>


	<?= !$model->getModel()->isNewRecord ? $form->field($model, 'username', [
		'options' => [
			'class' => 'col-md-3 col-lg-2',
		],
	])->textInput(['maxlength' => true]) : '' ?>

	<?= $form->field($model, 'email', [
		'options' => [
			'class' => 'col-md-3 col-lg-2',
		],
	])->textInput(['maxlength' => true]) ?>


</div>
<?= UserProfileFormWidget::widget([
	'model' => $model->getProfile(),
	'form' => $form,
]) ?>

<div class="row">

	<div class="col-md-10 col-lg-8">


		<?= AddressFormWidget::widget([
			'form' => $form,
			'model' => $model->getHomeAddress(),
		]) ?>


		<?= $form->field($model, 'traits')->widget(Select2::class, [
			'data' => CustomerUserForm::getTraitsNames(),
			'options' => [
				'multiple' => true,
			],
		]) ?>

	</div>
</div>


<div class="form-group">
	<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end() ?>

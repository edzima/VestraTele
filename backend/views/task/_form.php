<?php

use backend\modules\address\widgets\AddressWidget;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use common\models\User;

use yii\widgets\ActiveForm;

use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\Task */
/* @var $woj common\models\Task */
/* @var $accident common\models\Task */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'victim_name', ['template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-blind fa-lg"></i> Poszkodowany</span>{input}</div>'])->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'qualified_name')->textArea(['maxlength' => true, 'rows' => 3]) ?>

	<?= $form->field($model, 'phone',
		[
			'options' => ['class' => 'col-md-6 form-group'],
			'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-phone"></i> Numer</span>{input}</div>',
		])
		->widget(MaskedInput::class, [
			'mask' => '999-999-9999',
		])
	?>


	<?= $form->field($model, 'date',
		[
			'options' => ['class' => 'col-md-6 form-group'],
			'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i> Kiedy</span>{input}</div>',
		])
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
			])
	?>

	<?= $form->field($model, 'details',
		[
			'template' => '<div class="input-group">{input}<span class="input-group-addon"><i class="fa fa-pencil-square-o "></i> Szczegóły</span></div>',
		])->textArea(['rows' => 4]) ?>


	<?= $form->field($model, 'accident_id', ['options' => ['class' => 'col-md-6 form-group']])->dropDownList($accident) ?>

	<?= $form->field($model, 'meeting', ['options' => ['class' => 'col-md-3 form-group']])->dropDownList([0 => 'Nie', 1 => 'Tak']) ?>

	<?= $form->field($model, 'automat', ['options' => ['class' => 'col-md-3 form-group']])->dropDownList([0 => 'Nie', 1 => 'Tak']) ?>

	<?= $form->field($model, 'agent_id', ['template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-user-secret"></i> Przedstawiciel</span>{input}</div>'])->dropDownList(ArrayHelper::map(User::find()->where(['typ_work' => 'P'])->all(), 'id', 'username')) ?>

	<?= $form->field($model, 'tele_id', ['template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-user-secret"></i> Konsultant</span>{input}</div>'])->dropDownList(ArrayHelper::map(User::find()->where(['typ_work' => 'T'])->all(), 'id', 'username')) ?>

	<h3>Adres</h3>

	<?= AddressWidget::widget([
		'form' => $form,
		'model' => $model,
		'state' => 'woj',
		'province' => 'powiat',
		'subProvince' => 'gmina',
		'city' => 'city',
		'cityCode' => 'city_code',
	]);
	?>


	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? 'Dodaj' : 'Aktualizuj', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

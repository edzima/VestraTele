<?php

use common\models\PotentialClient;
use common\widgets\address\CitySimcInputWidget;
use common\widgets\DateWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PotentialClient */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="potential-client-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'status')->dropDownList(PotentialClient::getStatusesNames()) ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'details')->textarea(['rows' => 4]) ?>

	<?= $form->field($model, 'city_id')->widget(CitySimcInputWidget::class) ?>

	<?= $form->field($model, 'birthday')->widget(DateWidget::class) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

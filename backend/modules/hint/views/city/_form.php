<?php

use backend\modules\hint\models\HintCityForm;
use common\widgets\address\CitySimcInputWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model HintCityForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hint-city-form">

	<?php $form = ActiveForm::begin([
		'id' => 'hint-city-form',
	]); ?>

	<?= $form->field($model, 'city_id')->widget(CitySimcInputWidget::class) ?>

	<?= $form->field($model, 'user_id')->widget(Select2::class, ['data' => HintCityForm::getUsersNames()]) ?>

	<?= $form->field($model, 'type')->dropDownList(HintCityForm::getTypesNames()) ?>

	<?= $form->field($model, 'status')->dropDownList(HintCityForm::getStatusNames()) ?>

	<?= $form->field($model, 'details')->textarea(['rows' => 6]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('hint', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

<?php

use common\widgets\ActiveForm;
use common\widgets\PhoneInput;
use frontend\helpers\Html;
use frontend\models\HintCitySourceForm;
use yii\web\View;

/* @var $this View */
/* @var $model HintCitySourceForm */

?>

<div class="hint-city-source-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'source_id')->dropDownList($model->getSourcesNames()) ?>

	<?= $form->field($model, 'status')->dropDownList(HintCitySourceForm::getStatusesNames()) ?>

	<?= $form->field($model, 'rating')->dropDownList(HintCitySourceForm::getRatingsNames()) ?>

	<?= $form->field($model, 'phone')->widget(PhoneInput::class) ?>

	<?= $form->field($model, 'details')->textarea() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end() ?>
</div>

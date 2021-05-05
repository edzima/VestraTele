<?php

use common\widgets\ActiveForm;
use frontend\helpers\Html;
use frontend\models\HintCitySourceForm;

/* @var $this \yii\web\View */
/* @var $model HintCitySourceForm */

?>

<div class="hint-city-source-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'source_id')->dropDownList($model->getSourcesNames()) ?>

	<?= $form->field($model, 'rating')->dropDownList(HintCitySourceForm::getRatingsNames()) ?>

	<?= $form->field($model, 'phone')->textInput() ?>

	<?= $form->field($model, 'details')->textarea() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end() ?>
</div>

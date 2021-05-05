<?php

use common\models\hint\HintCitySource;
use common\models\hint\HintSource;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model HintCitySource */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hint-city-source-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'source_id')->dropDownList(ArrayHelper::map(HintSource::find()->asArray()->all(), 'id', 'name')) ?>

	<?= $form->field($model, 'hint_id')->textInput() ?>

	<?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'rating')->dropDownList(HintCitySource::getRatingsNames()) ?>

	<?= $form->field($model, 'status')->dropDownList(HintCitySource::getStatusesNames()) ?>

	<?= $form->field($model, 'details')->textarea(['rows' => 6]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('hint', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

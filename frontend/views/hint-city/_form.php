<?php

use common\widgets\ActiveForm;
use frontend\helpers\Html;
use frontend\models\HintCityForm;

/* @var $this \yii\web\View */
/* @var $model HintCityForm */

?>

<div class="hint-city-form">

	<?php $form = ActiveForm::begin([
		'id' => 'hint-city-form',
	]); ?>

	<?= $form->field($model, 'status')->dropDownList(HintCityForm::getStatusNames()) ?>

	<?= $form->field($model, 'details')->textarea(['rows' => 6]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

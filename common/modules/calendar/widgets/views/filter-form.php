<?php

use common\modules\calendar\models\Filter;
use kartik\color\ColorInput;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Filter */

?>

<?= $form->field($model, 'color')->widget(ColorInput::class, [
	'options' => ['placeholder' => Yii::t('calendar', 'Select color ...')],
]) ?>

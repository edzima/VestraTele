<?php

use common\modules\file\models\ValidatorOptions;
use kartik\number\NumberControl;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var ValidatorOptions $model */
/** @var ActiveForm $form */
?>

<div class="file-type-validator-form">

	<?= $form->field($model, 'extensions')->textInput() ?>

	<?= $form->field($model, 'maxSize')->widget(NumberControl::class) ?>

	<?= $form->field($model, 'maxFiles')->widget(NumberControl::class) ?>

</div>

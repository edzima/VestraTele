<?php

use common\modules\file\models\VisibilityOptions;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var VisibilityOptions $model */
/** @var ActiveForm $form */
?>

<div class="file-type-visibility-form">

	<?= $form->field($model, 'allowedRoles')->widget(Select2::class, [
		'data' => VisibilityOptions::getRolesNames(),
		'initValueText' => [],
		'options' => [
			'multiple' => true,
		],
		'pluginOptions' => [
			'placeholder' => Yii::t('file', 'Allowed Roles'),
		],
	]) ?>

	<?= $form->field($model, 'disallowedRoles')->widget(Select2::class, [
		'initValueText' => [],
		'data' => VisibilityOptions::getRolesNames(),
		'options' => [
			'multiple' => true,
		],
		'pluginOptions' => [

			'placeholder' => Yii::t('file', 'Disallowed Roles'),
		],
	]) ?>


</div>

<?php

use common\helpers\Html;
use common\models\forms\JsonModel;
use common\widgets\ActiveForm;

/** @var JsonModel $model */
/** @var ActiveForm|null $form */
/** @var array $formOptions */

?>

<div class="json-form">
	<?php
	if ($form === null) {
		$form = ActiveForm::begin($formOptions);
	}
	?>

	<?= $form->field($model, $model->getJsonAttribute())->textarea()->label(false) ?>

	<?= Html::submitButton(Yii::t('common', 'Load'), [
		'class' => 'btn btn-primary',
	]) ?>

	<?php
	ActiveForm::end();
	?>
</div>

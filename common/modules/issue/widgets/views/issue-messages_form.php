<?php

use common\models\message\IssueMessagesForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model IssueMessagesForm */
/* @var $checkboxesAttributes string[] */
?>
<fieldset>
	<legend><?= Yii::t('common', 'Notifications') ?></legend>

	<div class="issue-messages-form">
		<?php foreach ($checkboxesAttributes as $attribute): ?>
			<?= $form->field($model, $attribute)->checkbox() ?>
		<?php endforeach; ?>
		<?= $form->field($model, 'workersTypes')->widget(Select2::class, [
			'data' => $model->getWorkersUsersTypesNames(),
			'options' => [
				'multiple' => true,
				'placeholder' => $model->getAttributeLabel('workersTypes'),
			],
		]) ?>
	</div>
</fieldset>

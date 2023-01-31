<?php

use common\models\message\IssueMessagesForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model IssueMessagesForm */
/* @var $checkboxesAttributes string[] */
/* @var $withWorkersTypes bool */
/* @var $withExtraWorkers bool */

?>
<fieldset>
	<legend><?= Yii::t('common', 'Notifications') ?></legend>

	<div class="issue-messages-form">
		<?php foreach ($checkboxesAttributes as $attribute): ?>
			<?= $form->field($model, $attribute)->checkbox() ?>
		<?php endforeach; ?>

		<div class="row">


			<?= $withWorkersTypes
				? $form->field($model, 'workersTypes', [
					'options' => [
						'class' => 'col-md-6',
					],
				])->widget(Select2::class, [
					'data' => $model->getWorkersUsersTypesNames(),
					'options' => [
						'multiple' => true,
						'placeholder' => $model->getAttributeLabel('workersTypes'),
					],
				])
				: '' ?>

			<?= $withExtraWorkers && !empty($model->getExtraWorkersEmailsData())
				? $form->field($model, 'extraWorkersEmails', [
					'options' => [
						'class' => 'col-md-6',
					],
				])->widget(Select2::class, [
					'data' => $model->getExtraWorkersEmailsData(),
					'options' => [
						'multiple' => true,
						'placeholder' => $model->getAttributeLabel('extraWorkersEmails'),
					],
				])
				: '' ?>

		</div>
	</div>
</fieldset>

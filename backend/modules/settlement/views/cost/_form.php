<?php

use backend\helpers\Url;
use backend\modules\settlement\models\IssueCostForm;
use common\models\message\IssueCostMessagesForm;
use common\modules\issue\widgets\IssueMessagesFormWidget;
use common\widgets\ActiveForm;
use common\widgets\DateWidget;
use kartik\number\NumberControl;
use kartik\select2\Select2;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueCostForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $message IssueCostMessagesForm|null */

?>

<div class="issue-cost-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">

		<?= $form->field($model, 'user_id', ['options' => ['class' => 'col-md-4 col-lg-3']])
			->widget(Select2::class, [
				'data' => $model->getUserNames(),
				'pluginOptions' => [
					'placeholder' => Yii::t('common', 'Select...'),
					'allowClear' => true,
				],
			])
			->hint(
				$model->getModel()->isNewRecord
					? ($model->usersFromIssue
					? Html::a(Yii::t('issue', 'Not from Issues'), Url::current(['usersFromIssue' => 0]))
					: Html::a(Yii::t('issue', 'From Issues'), Url::current(['usersFromIssue' => 1]))
				)
					: null
			)
		?>

		<?= $form->field($model, 'type_id', ['options' => ['class' => 'col-md-4 col-lg-3']])->dropDownList($model->getTypesNames()) ?>

		<?= $form->field($model, 'transfer_type', ['options' => ['class' => 'col-md-4 col-lg-2']])->dropDownList(IssueCostForm::getTransfersTypesNames(), ['prompt' => Yii::t('common', 'Select...')]) ?>


	</div>

	<div class="row">
		<?= $form->field($model, 'date_at', ['options' => ['class' => 'col-md-4 col-lg-2']])->widget(DateWidget::class) ?>

		<?= $form->field($model, 'settled_at', ['options' => ['class' => 'col-md-4 col-lg-2']])->widget(DateWidget::class) ?>

		<?= $form->field($model, 'deadline_at', ['options' => ['class' => 'col-md-4 col-lg-2']])->widget(DateWidget::class) ?>

		<?= $form->field($model, 'confirmed_at', ['options' => ['class' => 'col-md-4 col-lg-2']])->widget(DateWidget::class) ?>
	</div>


	<div class="row">
		<?= $form->field($model, 'value', ['options' => ['class' => 'col-xs-9 col-md-3 col-lg-2']])->widget(NumberControl::class) ?>

		<?= $form->field($model, 'vat', ['options' => ['class' => 'col-xs-3 col-md-2']])->widget(NumberControl::class) ?>

	</div>

	<div class="row">
		<div class="col-md-6">
			<?= isset($message)
				? IssueMessagesFormWidget::widget([
					'model' => $message,
					'form' => $form,
					'checkboxesAttributes' => [
						'sendSmsToCustomer',
						'sendEmailToWorkers',
					],
				])
				: ''
			?>
		</div>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

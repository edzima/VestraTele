<?php

use common\modules\address\widgets\AddressWidget;
use common\models\issue\IssueMeet;
use common\models\User;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueMeet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-meet-form">

	<?php $form = ActiveForm::begin(); ?>

	<?php if ($model->isNewRecord): ?>
		<?= $form->field($model, 'type_id')->dropDownList(IssueMeet::getTypesNames()) ?>
		<?= $form->field($model, 'campaign_id')->dropDownList(IssueMeet::getCampaignNames()) ?>
	<?php endif; ?>

	<?= $form->field($model, 'status')->dropDownList(IssueMeet::getStatusNames()) ?>

	<?php if (
		Yii::$app->user->can(User::ROLE_TELEMARKETER)
		&& $model->tele_id === Yii::$app->user->getId()

	): ?>
		<fieldset>
			<legend>Klient</legend>
			<div class="row">
				<?= $form->field($model, 'client_name', [
					'options' => [
						'class' => 'col-md-4',
					],
				])->textInput([
				]) ?>

				<?= $form->field($model, 'client_surname', [
					'options' => [
						'class' => 'col-md-4',
					],
				])->textInput([
				]) ?>

				<?= $form->field($model, 'phone', [
					'options' => [
						'class' => 'col-md-4',
					],
				])->textInput(['maxlength' => true]) ?>
			</div>
			<?= AddressWidget::widget([
				'form' => $form,
				'model' => $model,
				'state' => 'stateId',
				'province' => 'provinceId',
				'subProvince' => 'sub_province_id',
				'city' => 'city_id',
				'street' => 'street',
			]) ?>
		</fieldset>

		<?= $form->field($model, 'agent_id')
			->widget(Select2::class, [
					'data' => User::getSelectList([User::ROLE_AGENT]),
					'options' => [
						'placeholder' => 'Agent',
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>


	<?php endif; ?>


	<?= $form->field($model, 'date_at')
		->widget(DateTimeWidget::class,
			[
				//	'phpDatetimeFormat' => 'yyyy-MM-dd',
				'clientOptions' => [

					'allowInputToggle' => true,
					'sideBySide' => true,
					'widgetPositioning' => [
						'horizontal' => 'auto',
						'vertical' => 'auto',
					],
				],
			]) ?>

	<?= $form->field($model, 'details')->textarea(['rows' => 6]) ?>

	<div class="form-group">
		<?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

<?php

use common\helpers\Html;
use common\models\user\User;
use common\modules\lead\models\searches\LeadSearch;
use common\widgets\ActiveForm;
use common\widgets\address\AddressSearchWidget;
use common\widgets\DateWidget;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model LeadSearch */
/* @var $form ActiveForm */

?>

<div class="lead-search">

	<?php $form = ActiveForm::begin([
		'method' => 'get',
		'id' => 'lead-header-filter-form',
	]); ?>

	<div class="row">


		<?= $form->field($model, 'email', ['options' => ['class' => 'col-md-2 col-lg-1']])->textInput([
			'type' => 'email',
		]) ?>

		<?= $form->field($model, 'provider', ['options' => ['class' => 'col-md-2 col-lg-1']])->widget(Select2::class, [
			'data' => LeadSearch::getProvidersNames(),
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('provider'),
				'allowClear' => true,
			],
		]) ?>


		<?= $form->field($model, 'campaign_id', ['options' => ['class' => 'col-md-2 col-lg-2']])->widget(Select2::class, [
			'data' => $model->getCampaignNames(),
			'pluginOptions' => [
				'placeholder' => Yii::t('lead', 'Campaign'),
				'allowClear' => true,
			],
		]) ?>

		<?php if ($model->scenario !== LeadSearch::SCENARIO_USER): ?>

			<?= $form->field($model, 'user_id', ['options' => ['class' => 'col-md-3 col-lg-2']])->widget(Select2::class, [
				'data' => LeadSearch::getUsersNames(),
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('user_id'),
					'allowClear' => true,
					'multiple' => true,
				],
			])
			?>

			<?= $form->field($model, 'user_type', ['options' => ['class' => 'col-md-2 col-lg-1']])->widget(Select2::class, [
				'data' => LeadSearch::getUserTypesNames(),
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('user_type'),
					'allowClear' => true,
				],
			])
			?>



			<?= $form->field($model, 'withoutUser', ['options' => ['class' => 'col-md-2']])->checkbox() ?>

			<?= $form->field($model, 'duplicatePhone', ['options' => ['class' => 'col-md-1']])->dropDownList(Html::booleanDropdownList(), [
				'prompt' => Yii::t('lead', 'Select...'),
			]) ?>

			<?= $form->field($model, 'duplicateEmail', ['options' => ['class' => 'col-md-1']])->checkbox() ?>


		<?php else: ?>

			<?= $form->field($model, 'selfUserId', ['options' => ['class' => 'col-md-3 col-lg-2']])->widget(Select2::class, [
				'data' => $model->getSelfUsersNames(),
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('user_id'),
					'allowClear' => true,
				],
			])
				->label($model->getAttributeLabel('user_id'))
			?>
		<?php endif; ?>

	</div>


	<div class="row">

		<?= $form->field($model, 'closedQuestions', ['options' => ['class' => 'col-md-6']])->widget(Select2::class, [
			'data' => LeadSearch::getClosedQuestionsNames(),
			'options' => ['multiple' => true,],
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('closedQuestions'),
				'allowClear' => true,
			],
		])
		?>

		<?= $form->field($model, 'withoutArchives', ['options' => ['class' => 'col-md-2']])->checkbox() ?>

		<?= $form->field($model, 'withoutReport', ['options' => ['class' => 'col-md-2']])->checkbox() ?>

		<?= $form->field($model, 'withAddress', ['options' => ['class' => 'col-md-1']])->checkbox() ?>


	</div>

	<div class="row">
		<?= $form->field($model, 'from_at', [
			'options' => [
				'class' => [
					'col-md-3 col-lg-2',
				],
			],
		])->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'to_at', [
			'options' => [
				'class' => [
					'col-md-3 col-lg-2',
				],
			],
		])->widget(DateWidget::class)

		?>

		<?= $form->field($model, 'hoursAfterLastReport', ['options' => ['class' => 'col-md-2 col-lg-1']])->textInput([
			'type' => 'number',
			'step' => 1,
		]) ?>


		<?= $form->field($model, 'olderByDays', ['options' => ['class' => 'col-md-2 col-lg-1']])->textInput([
			'type' => 'number',
			'min' => 1,
			'step' => 1,
		]) ?>


		<?= Yii::$app->user->can(User::PERMISSION_LEAD_MARKET) ?
			$form->field($model, 'fromMarket', ['options' => ['class' => 'col-md-1']])->dropDownList(Html::booleanDropdownList(), [
				'prompt' => Yii::t('common', 'All'),
			])
			: ''
		?>

		<?= $form->field($model, 'reportStatus', ['options' => ['class' => 'col-md-2']])->widget(Select2::class, [
			'data' => LeadSearch::getStatusNames(),
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('reportStatus'),
			],
		])
		?>


	</div>

	<?= AddressSearchWidget::widget([
		'form' => $form,
		'model' => $model->getAddressSearch(),
	]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>

		<?= Html::a(Yii::t('backend', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

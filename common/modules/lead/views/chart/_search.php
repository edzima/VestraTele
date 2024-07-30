<?php

use common\helpers\Html;
use common\models\user\User;
use common\modules\lead\models\searches\LeadChartSearch;
use common\widgets\ActiveForm;
use common\widgets\DateWidget;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model LeadChartSearch */
/* @var $form ActiveForm */
/* @var $sourcesNames array */
$usersNames = LeadChartSearch::getUsersNames();
?>

<div class="lead-chart-search-search">

	<?php $form = ActiveForm::begin([
		'method' => 'get',
		'id' => 'lead-chart-filter-form',
		'action' => ['index'],
	]); ?>


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


	</div>

	<div class="row">

		<?= $form->field($model, 'excludedStatus', ['options' => ['class' => 'col-md-2']])->widget(Select2::class, [
			'data' => LeadChartSearch::getStatusNames(),
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('excludedStatus'),
				'multiple' => true,
				'allowClear' => true,
			],
		])
		?>

		<?= $form->field($model, 'status_id', ['options' => ['class' => 'col-md-2']])->widget(Select2::class, [
			'data' => LeadChartSearch::getStatusNames(),
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('status_id'),
				'multiple' => true,
				'allowClear' => true,
			],
		])
		?>


		<?= $form->field($model, 'reportStatus', ['options' => ['class' => 'col-md-2']])->widget(Select2::class, [
			'data' => LeadChartSearch::getStatusNames(),
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('reportStatus'),
				'allowClear' => true,
			],
		])
		?>

		<?= $form->field($model, 'groupedStatus', ['options' => ['class' => 'col-md-2']])
			->dropDownList(LeadChartSearch::statusGroupNames())
		?>


		<?= $form->field($model, 'groupedStatusChartType', ['options' => ['class' => 'col-md-2']])
			->dropDownList(LeadChartSearch::statusGroupChartTypesNames())
		?>

	</div>

	<div class="row">
		<?=
		$form->field($model, 'type_id', ['options' => ['class' => 'col-md-2 col-lg-2']])->widget(Select2::class, [
			'data' => LeadChartSearch::getTypesNames(),
			'pluginOptions' => [
				'placeholder' => Yii::t('lead', 'Type'),
				'allowClear' => true,
				'multiple' => true,
			],
		])->label(Yii::t('lead', 'Type'))
		?>

		<?= !empty($sourcesNames) ?
			$form->field($model, 'source_id', ['options' => ['class' => 'col-md-2 col-lg-2']])->widget(Select2::class, [
				'data' => $sourcesNames,
				'pluginOptions' => [
					'placeholder' => Yii::t('lead', 'Source'),
					'allowClear' => true,
					'multiple' => true,
				],
			])
			: '' ?>


		<?= $form->field($model, 'campaign_id', ['options' => ['class' => 'col-md-2 col-lg-2']])->widget(Select2::class, [
			'data' => $model->getCampaignNames(),
			'pluginOptions' => [
				'placeholder' => Yii::t('lead', 'Campaign'),
				'allowClear' => true,
			],
		]) ?>


		<?= $form->field($model, 'provider', ['options' => ['class' => 'col-md-2 col-lg-2']])->widget(Select2::class, [
			'data' => LeadChartSearch::getProvidersNames(),
			'pluginOptions' => [
				'placeholder' => Yii::t('lead', 'Provider'),
				'allowClear' => true,
			],
		]) ?>


		<?= Yii::$app->user->can(User::PERMISSION_LEAD_MARKET) ?
			$form->field($model, 'fromMarket', ['options' => ['class' => 'col-md-1']])->dropDownList(Html::booleanDropdownList(), [
				'prompt' => Yii::t('common', 'All'),
			])
			: ''
		?>

		<?= Yii::$app->user->can(User::PERMISSION_LEAD_COST)
			? $form->field($model, 'onlyWithCosts', ['options' => ['class' => 'col-md-1']])->checkbox()
			: ''
		?>

		<?php if ($model->scenario !== LeadChartSearch::SCENARIO_USER): ?>

			<?= $form->field($model, 'user_id', ['options' => ['class' => 'col-md-3 col-lg-2']])->widget(Select2::class, [
				'data' => $usersNames,
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('user_id'),
					'allowClear' => true,
					'multiple' => true,
				],
			])

			?>

			<?= $form->field($model, 'user_type', ['options' => ['class' => 'col-md-2 col-lg-1']])->widget(Select2::class, [
				'data' => LeadChartSearch::getUserTypesNames(),
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('user_type'),
					'allowClear' => true,
				],
			])

			?>

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


	<!--	<div class="row">-->
	<!---->
	<!---->
	<!--		--><?php //= $form->field($model, 'onlyWithEmail', ['options' => ['class' => 'col-md-2']])->checkbox() ?>
	<!---->
	<!--		--><?php //= $form->field($model, 'onlyWithPhone', ['options' => ['class' => 'col-md-2']])->checkbox() ?>
	<!---->
	<!--		--><?php //= $form->field($model, 'withoutArchives', ['options' => ['class' => 'col-md-2']])->checkbox() ?>
	<!---->
	<!--		--><?php //= $form->field($model, 'withoutReport', ['options' => ['class' => 'col-md-2']])->checkbox() ?>
	<!---->
	<!--		--><?php //= $form->field($model, 'withAddress', ['options' => ['class' => 'col-md-1']])->checkbox() ?>
	<!---->
	<!---->
	<!--	</div>-->


	<!--	--><?php //= AddressSearchWidget::widget([
	//		'form' => $form,
	//		'model' => $model->getAddressSearch(),
	//	]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>

		<?= Html::a(Yii::t('backend', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

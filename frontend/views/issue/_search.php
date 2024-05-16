<?php

use common\widgets\ActiveForm;
use common\widgets\address\AddressSearchWidget;
use common\widgets\DateWidget;
use frontend\helpers\Html;
use frontend\helpers\Url;
use frontend\models\search\IssueSearch;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model IssueSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div id="issue-search" class="issue-search">

	<?php $form = ActiveForm::begin([
		'method' => 'get',
		'options' => [
			'data-pjax' => 1,
		],
		'action' => null,
	]); ?>

	<div class="row">

		<?= $form->field($model, 'createdAtFrom', ['options' => ['class' => 'col-md-3 col-lg-2']])
			->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'createdAtTo', ['options' => ['class' => 'col-md-3 col-lg-2']])
			->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'tagsIds', ['options' => ['class' => 'col-md-8 col-lg-6']])->widget(Select2::class, [
			'data' => IssueSearch::getTagsNames(),
			'options' => [
				'multiple' => true,
				'placeholder' => Yii::t('issue', 'Tags'),
			],
			'pluginOptions' => [
				'allowClear' => true,
			],
			'showToggleAll' => false,
		]) ?>

	</div>

	<div class="row">
		<?= $form->field($model, 'excludedTypes', ['options' => ['class' => 'col-md-5 col-lg-4']])->widget(Select2::class, [
			'data' => $model->getIssueTypesNames(),
			'options' => [
				'multiple' => true,
				'placeholder' => $model->getAttributeLabel('excludedTypes'),
			],
			'pluginOptions' => [
				'allowClear' => true,
			],
		]) ?>

		<?= $form->field($model, 'excludedStages', ['options' => ['class' => 'col-md-5 col-lg-4']])->widget(Select2::class, [
			'data' => $model->getIssueStagesNames(),
			'options' => [
				'multiple' => true,
				'placeholder' => $model->getAttributeLabel('excludedStages'),
			],
			'pluginOptions' => [
				'allowClear' => true,
			],
		]) ?>


		<?= $form->field($model, 'onlyWithTelemarketers', ['options' => ['class' => 'col-md-2']])
			->dropDownList(Html::booleanDropdownList(), [
				'prompt' => Yii::t('common', 'All'),
			])
		?>
	</div>


	<?= $model->addressSearch !== null
		? AddressSearchWidget::widget([
			'form' => $form,
			'model' => $model->addressSearch,
		])
		: ''
	?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('frontend', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', ['index', Url::PARAM_ISSUE_PARENT_TYPE => $model->parentTypeId], ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

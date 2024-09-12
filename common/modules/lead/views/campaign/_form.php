<?php

use common\helpers\ArrayHelper;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\Module;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadCampaign */
/* @var $form yii\widgets\ActiveForm */

$parents = LeadCampaign::getModels();
if ($model->id) {
	unset($parents[$model->id]);
}
$parents = ArrayHelper::map($parents, 'id', 'name');
?>

<div class="lead-campaign-form">

	<?php $form = ActiveForm::begin(
		['id' => 'lead-campaign-form']
	); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $model->getScenario() !== LeadCampaign::SCENARIO_OWNER
		? $form->field($model, 'owner_id')->widget(Select2::class, [
			'data' => Module::userNames(),
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('owner_id'),
				'allowClear' => true,
			],
		])
		: '' ?>

	<?= $form->field($model, 'is_active')->checkbox() ?>

	<?= $form->field($model, 'type')->dropDownList(LeadCampaign::getTypesNames(), [
		'prompt' => Yii::t('lead', '--- Select ---'),
	]) ?>

	<?= $form->field($model, 'parent_id')->widget(Select2::class, [
			'data' => $parents,
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('parent_id'),
				'allowClear' => true,
			],
		]
	)
	?>
	<?= $form->field($model, 'details')->textarea() ?>

	<?= $form->field($model, 'entity_id')->textInput() ?>


	<?= $form->field($model, 'sort_index')->textInput() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

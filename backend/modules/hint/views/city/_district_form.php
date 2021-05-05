<?php

use backend\helpers\Url;
use backend\modules\hint\models\HintDistrictForm;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model HintDistrictForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hint-district-form">

	<?php $form = ActiveForm::begin([
		'id' => 'hint-district-form',
	]); ?>

	<div class="row">

		<?= $form->field($model, 'region_id', [
			'options' => ['class' => 'col-md-4'],
		])
			->widget(Select2::class, [
					'data' => HintDistrictForm::getRegionsNames(),
					'options' => [
						'id' => Html::getInputId($model, 'region_id'),
					],
				]
			)
		?>

		<?= $form->field($model, 'district_id', [
			'options' => ['class' => 'col-md-4'],
		])->widget(DepDrop::class, [
			'type' => DepDrop::TYPE_SELECT2,
			'options' => [
				'id' => Html::getInputId($model, 'district_id'),
			],
			'pluginOptions' => [
				'depends' => [Html::getInputId($model, 'region_id')],
				'url' => Url::to(['/teryt/terc/district-list']),
				'loading' => Yii::t('common', 'Loading...'),
				'params' => [Html::getInputId($model, 'district_id')],
			],
		]); ?>

		<?= $form->field($model, 'commune_id', [
			'options' => ['class' => 'col-md-4'],
		])
			->widget(DepDrop::class, [
				'type' => DepDrop::TYPE_SELECT2,
				'pluginOptions' => [
					'depends' => [
						Html::getInputId($model, 'region_id'),
						Html::getInputId($model, 'district_id'),
					],
					'url' => Url::to(['/teryt/terc/commune-list']),
					'loading' => Yii::t('common', 'Loading...'),
				],
			])
		?>


	</div>

	<?= $form->field($model, 'user_id')->widget(Select2::class, ['data' => HintDistrictForm::getUsersNames()]) ?>

	<?= $form->field($model, 'type')->dropDownList(HintDistrictForm::getTypesNames()) ?>

	<?= $form->field($model, 'details')->textarea(['rows' => 6]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('hint', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

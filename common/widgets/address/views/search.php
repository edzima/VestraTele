<?php

use common\helpers\Html;
use common\helpers\Url;
use common\models\AddressSearch;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model AddressSearch */
?>
<div class="address-search-widget">
	<div class="row">

		<?= $form->field($model, 'region_id', [
			'options' => ['class' => 'col-md-4 col-lg-2'],
		])
			->widget(Select2::class, [
					'data' => $model->getRegionsData(),
					'options' => [
						'id' => Html::getInputId($model, 'region_id'),
						'placeholder' => $model->getAttributeLabel('region_id'),
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
					'pluginEvents' => [
						'select2:clear' => new JsExpression('function(){
							setTimeout(function(){
								$("#' . Html::getInputId($model, 'commune_id') . '").val(null).trigger("change");
								$("#' . Html::getInputId($model, 'district_id') . '").val(null).trigger("change");
							},500);
						}'),
					],

				]
			)
		?>

		<?= $form->field($model, 'district_id', [
			'options' => ['class' => 'col-md-4 col-lg-2'],
		])->widget(DepDrop::class, [
			'type' => DepDrop::TYPE_SELECT2,
			'options' => [
				'id' => Html::getInputId($model, 'district_id'),
				'placeholder' => Yii::t('common', 'Select...'),
			],
			'data' => $model->getDistrictsData(),
			'pluginOptions' => [
				'depends' => [Html::getInputId($model, 'region_id')],
				'url' => Url::to(['/teryt/terc/district-list']),
				'loading' => Yii::t('common', 'Loading...'),
				'params' => [Html::getInputId($model, 'district_id')],
			],
			'pluginEvents' => [],
			'select2Options' => [
				'pluginOptions' => [
					'allowClear' => true,
				],
				'pluginEvents' => [
					'select2:clear' => new JsExpression('function(){
							setTimeout(function(){
								$("#' . Html::getInputId($model, 'commune_id') . '").val(null).trigger("change");
							},500);
						}'),
				],

			],
		]); ?>

		<?= $form->field($model, 'commune_id', [
			'options' => ['class' => 'col-md-4 col-lg-2'],
		])
			->widget(DepDrop::class, [
				'type' => DepDrop::TYPE_SELECT2,
				'data' => $model->getCommunesData(),
				'options' => [
					Html::getInputId($model, 'commune_id'),
					'placeholder' => Yii::t('common', 'Select...'),
				],
				'pluginOptions' => [
					'depends' => [
						Html::getInputId($model, 'region_id'),
						Html::getInputId($model, 'district_id'),
					],
					'url' => Url::to(['/teryt/terc/commune-list']),
					'loading' => Yii::t('common', 'Loading...'),
				],
				'select2Options' => [
					'pluginOptions' => [
						'allowClear' => true,
					],
				],
			])
		?>



		<?= $form->field($model, 'postal_code', [
			'options' => [
				'class' => 'col-md-2 col-lg-1',
			],
		])->textInput() ?>


		<?= $form->field($model, 'city_name', [
			'options' => [
				'class' => 'col-md-4 col-lg-3',
			],
		])->textInput() ?>
	</div>
</div>


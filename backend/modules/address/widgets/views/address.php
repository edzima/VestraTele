<?php

use backend\modules\address\widgets\AddressFormWidget;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $id string */
/* @var $legend string */
/* @var $model \yii\base\Model */
/* @var $form yii\widgets\ActiveForm */
/* @var $state string */
/* @var $stateAdd string */
/* @var $province string */
/* @var $provinceAdd string */
/* @var $subProvince string */
/* @var $subProvinceAdd string */
/* @var $city string */
/* @var $cityAdd string */
/* @var $street string */
/* @var $cityCode string */
/* @var $copyOptions array */

$mergeOptions = function (string $attribute, array $options) use ($copyOptions) {
	if (empty($copyOptions) || !isset($copyOptions['inputs'][$attribute])) {
		return $options;
	}
	$options[$copyOptions['data-selector']] = $copyOptions['inputs'][$attribute];
	return $options;
};

$template = function (string $attribute, ?string $url) {
	$content = "{input}";
	if ($url !== null) {
		$content .= Html::a('<i class="fa fa-plus"></i>', Url::toRoute($url), ['class' => 'input-group-addon', 'target' => '_blank']);
	}
	return Html::tag('div', $content, ['class' => 'input-group']) . "\n{error}";
}

?>
<fieldset>
	<legend><?= Html::encode($legend) ?></legend>
	<div class="row">
		<?php if (!empty($state)): ?>
			<?= $form->field
			($model,
				$state, [
					'options' => ['class' => 'col-md-' . ($subProvince === null ? '6' : '4')],
					'template' => $template('state', $stateAdd),
				])
				->widget(Select2::class, [
						'data' => AddressFormWidget::getStates(),
						'initValueText' => $model->{$state} ?? null,
						'options' => $mergeOptions('state', [
							'placeholder' => '--Wybierz województwo--',
							'id' => $id . 'state-id',
						]),
					]
				)->label(false); ?>
		<?php endif; ?>

		<?php if (!empty($province)): ?>
			<?= $form->field(
				$model,
				$province,
				[
					'options' => ['class' => 'form-group col-md-' . ($subProvince === null ? '6' : '4')],
					'template' => $template('province', $provinceAdd),
				])->widget(DepDrop::class, [
				'type' => DepDrop::TYPE_SELECT2,
				'options' =>
					['id' => $id . 'province-id'],
				'data' => $model->{$state} > 0 ? AddressFormWidget::getProvinces($model->{$state}) : [],
				'pluginOptions' => $mergeOptions('province', [
					'depends' => [$id . 'state-id'],
					'placeholder' => 'Powiat...',
					'url' => Url::to(['/address/city/powiat']),
					'loading' => 'wyszukiwanie...',
					'params' => [$id . 'province-id'],
				]),
			]); ?>
		<?php endif; ?>

		<?php if (!empty($subProvince)): ?>
			<?= $form->field(
				$model,
				$subProvince, [
				'options' => ['class' => 'col-md-4'],
				'template' => $template('subProvince', $subProvinceAdd),
			])
				->widget(DepDrop::class, [
					'type' => DepDrop::TYPE_SELECT2,
					'data' => $model->{$state} > 0 && $model->{$province} > 0 ? AddressFormWidget::getSubprovinces($model->{$state}, $model->{$province}) : [],
					'pluginOptions' => [
						'depends' => [$id . 'state-id', $id . 'province-id'],
						'placeholder' => 'Gmina...',
						'url' => Url::to(['/address/city/gmina']),
					],
				])->label(false)
			?>
		<?php endif; ?>

	</div>
	<div class="row">
		<?php if (!empty($city)): ?>

			<?= $form->field($model, $city,
				[
					'options' => ['class' => 'col-md-8 form-group'],
					'template' => $template('city', $cityAdd),
				])
				->widget(DepDrop::class, [
					'type' => DepDrop::TYPE_SELECT2,
					'data' => $model->{$state} > 0 && $model->{$province} > 0 ? AddressFormWidget::getCities($model->{$state}, $model->{$province}) : [],
					'pluginOptions' => [
						'depends' => [$id . 'state-id', $id . 'province-id'],
						'placeholder' => 'Miejscowość...',
						'url' => Url::to(['/address/city/city']),
					],
				]);
			?>
		<?php endif; ?>


		<?php if (!empty($cityCode)): ?>
			<?= $form->field($model, $cityCode,
				[
					'options' => ['class' => 'col-md-4 form-group'],
					'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-barcode"></i> Kod</span>{input}</div>',
				])
				->widget(MaskedInput::class, [
					'mask' => '99-999',
					'options' => $mergeOptions('cityCode', ['class' => 'form-control']),
				]);
			?>
		<?php endif; ?>
	</div>

	<?php if (!empty($street)): ?>
		<?= $form->field($model, $street)->textInput($mergeOptions('street', ['maxlength' => true])) ?>
	<?php endif; ?>

</fieldset>

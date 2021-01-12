<?php

use common\models\AddressSearch;
use kartik\select2\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model AddressSearch */
?>
<div class="address-search-widget">
	<div class="row">
		<?=
		$form->field($model, 'region_id', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->widget(Select2::class, [
			'data' => $model->getRegionsNames(),
			'options' => [
				'placeholder' => $model->getAttributeLabel('region_id'),
			],
		])
		?>

		<?= $form->field($model, 'postal_code', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->textInput() ?>


		<?= $form->field($model, 'city_name', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->textInput() ?>
	</div>
</div>


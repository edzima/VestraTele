<?php

use common\widgets\address\AddressSearchWidget;
use common\widgets\DateWidget;
use frontend\models\search\PotentialClientSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PotentialClientSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $withAddress bool */
/* @var $withFirstname bool */
/* @var $withLastname bool */
/* @var $action string */

?>

<div class="potential-client-search">

	<?php $form = ActiveForm::begin([
		'action' => [$action],
		'method' => 'get',
	]); ?>

	<div class="row">

		<?= $withFirstname
			? $form->field($model, 'firstname', [
				'options' => [
					'class' => 'col-md-3',
				],
			])->textInput()
			: ''
		?>
		<?= $withLastname
			? $form->field($model, 'lastname', [
				'options' => [
					'class' => 'col-md-3',
				],
			])->textInput()
			: ''
		?>

		<?= $form->field($model, 'birthday', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->widget(DateWidget::class) ?>
	</div>

	<?= $withAddress
		? AddressSearchWidget::widget([
			'model' => $model->getAddressSearch(),
			'form' => $form,
			'withPostalCode' => false,
		])
		: '' ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('common', 'Reset'), [$action], ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

<?php

use common\widgets\address\AddressSearchWidget;
use common\widgets\DateWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\search\PotentialClientSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="potential-client-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'birthday', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->widget(DateWidget::class) ?>
	</div>

	<?= AddressSearchWidget::widget([
		'model' => $model->getAddressSearch(),
		'form' => $form,
		'withPostalCode' => false,
	]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('common', 'Reset'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

<?php

use common\modules\lead\models\searches\LeadMarketSearch;
use common\widgets\address\AddressSearchWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadMarketSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-market-search">

	<?php $form = ActiveForm::begin([
		//'action' => '',,
		'method' => 'get',
	]); ?>


	<?= AddressSearchWidget::widget([
		'form' => $form,
		'model' => $model->addressSearch,
	]) ?>

	<div class="row">
		<?= $form->field($model, 'withoutSelfAssign')->checkbox() ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Reset'), 'user', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

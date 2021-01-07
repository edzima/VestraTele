<?php

use backend\modules\settlement\models\search\PayReceivedSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\settlement\models\search\PayReceivedSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pay-received-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'transferStatus')->dropDownList(PayReceivedSearch::getTransferStatusNames(), ['prompt' => Yii::t('settlement', 'Set transfer status')]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('settlement', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('settlement', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

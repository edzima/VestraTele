<?php

use common\modules\lead\models\LeadMarket;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadMarket */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-market-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'lead_id')->textInput() ?>

	<?= $form->field($model, 'status')->textInput() ?>

	<?= $form->field($model, 'options')->textarea(['rows' => 6]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

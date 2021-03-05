<?php

use backend\helpers\Html;
use backend\modules\provision\models\SettlementUserProvisionsForm;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model SettlementUserProvisionsForm */
/* @var $form ActiveForm */
?>

<div class="settlement-provision-user-form">

	<?php $form = ActiveForm::begin([
			'id' => 'settlement-user-provisions',
		]
	); ?>

	<?= $form->field($model, 'typeId')->dropDownList($model->getTypesNames())->hiddenInput()->label(false) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Generate'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>


<?php

use common\helpers\Html;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\widgets\ReportFormWidget;
use common\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $model ReportForm */

?>
	<div class="lead-report-form">

		<?php $form = ActiveForm::begin([
			'id' => 'lead-report-form',
		]); ?>

		<?= ReportFormWidget::widget([
			'form' => $form,
			'model' => $model,
		]) ?>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>

<?php

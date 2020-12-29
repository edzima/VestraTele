<?php

use common\models\issue\IssuePay;
use common\widgets\DateWidget;
use frontend\models\UpdatePayForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UpdatePayForm */
/* @var $form ActiveForm */
?>

<div class="issue-pay-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">

		<?= $form->field($model, 'deadline_at', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'status', ['options' => ['class' => 'col-md-2']])
			->dropDownList(IssuePay::getStatusNames(), [
				'prompt' => Yii::t('common', 'Status...'),
			])
		?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

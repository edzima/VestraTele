<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMultipleSmsForm;
use common\modules\lead\models\LeadSmsForm;
use common\widgets\ActiveForm;
use yii\web\View;

/* @var $this View */
/* @var $model LeadMultipleSmsForm */

$this->title = Yii::t('lead', 'Send multiple SMS to {count} Leads', [
	'count' => count($model->getModels()),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="lead-sms-push">
	<h1><?= Html::encode($this->title) ?></h1>

	<div class="lead-sms-push-form">
		<?php $form = ActiveForm::begin(['id' => 'lead-multiple-sms-push-form']) ?>

		<?= $form->field($model, 'status_id')->dropDownList(LeadSmsForm::getStatusNames()) ?>

		<?= $form->field($model, 'message')->textarea() ?>

		<?= $form->field($model, 'withOverwrite')->checkbox() ?>

		<?= $form->field($model, 'removeSpecialCharacters')->checkbox() ?>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('lead', 'Send'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end() ?>
	</div>
</div>

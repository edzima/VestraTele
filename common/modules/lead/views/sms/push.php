<?php

use common\helpers\Html;
use common\modules\lead\models\LeadSmsForm;
use common\widgets\ActiveForm;
use yii\web\View;

/* @var $this View */
/* @var $model LeadSmsForm */

$this->title = Yii::t('lead', 'Send SMS to Lead: {lead}', ['lead' => $model->getLead()->getName()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getLead()->getName(), 'url' => ['lead/view', 'id' => $model->getLead()->getId()]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="lead-sms-push">
	<h1><?= Html::encode($this->title) ?></h1>

	<div class="lead-sms-push-form">
		<?php $form = ActiveForm::begin(['id' => 'lead-sms-push-form']) ?>

		<?= $form->field($model, 'status_id')->dropDownList(LeadSmsForm::getStatusNames()) ?>

		<?= $form->field($model, 'phone')->textInput(['readonly' => true]) ?>

		<?= $form->field($model, 'message')->textarea() ?>

		<?= $form->field($model, 'withOverwrite')->checkbox() ?>

		<?= $form->field($model, 'removeSpecialCharacters')->checkbox() ?>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('lead', 'Send'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end() ?>
	</div>
</div>

<?php

use common\helpers\Html;
use common\modules\lead\models\forms\LeadStatusChangeForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model LeadStatusChangeForm */

$this->title = Yii::t('lead', 'Change Status for Leads: {count}', ['count' => count($model->ids)]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Statuses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="lead-status-change">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin([
		'id' => 'lead-status-change-form',
	]); ?>


	<?= $form->field($model, 'status_id')->widget(Select2::class, [
		'data' => LeadStatusChangeForm::getStatusNames(),
	]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

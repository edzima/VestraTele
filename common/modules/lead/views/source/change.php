<?php

use common\helpers\Html;
use common\modules\lead\models\forms\LeadSourceChangeForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model LeadSourceChangeForm */

$this->title = Yii::t('lead', 'Change Source for Leads: {count}', ['count' => count($model->ids)]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Sources'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="lead-source-change">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin([
		'id' => 'lead-source-change-form',
	]); ?>

	<?= Html::hiddenInput('leadsIds', implode(',', $model->ids)) ?>

	<?= $form->field($model, 'source_id')->widget(Select2::class, [
		'data' => LeadSourceChangeForm::getSourcesNames(),
	]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

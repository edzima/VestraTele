<?php

use backend\helpers\Html;
use backend\modules\issue\models\IssuesUpdateTypeMultiple;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model IssuesUpdateTypeMultiple */

$this->title = Yii::t('issue', 'Change Type to Issues: {count}', ['count' => count($model->ids)]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="issue-sms-send-multiple">
	<?php $form = ActiveForm::begin([
		'id' => 'issues-change-type-multiple-form',
	]) ?>

	<?= Html::hiddenInput('ids', implode(',', $model->ids)) ?>


	<?= $form->field($model, 'typeId')->widget(Select2::class, [
		'data' => IssuesUpdateTypeMultiple::getTypesNames(),
	]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end() ?>
</div>

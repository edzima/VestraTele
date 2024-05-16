<?php

use common\modules\lead\models\forms\LeadDeadlineForm;
use common\widgets\ActiveForm;
use common\widgets\DateWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadDeadlineForm */

$lead = $model->getLead();
$this->title = Yii::t('lead', 'Update Deadline: {name}', [
	'name' => $lead->getName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $lead->getName(), 'url' => ['view', 'id' => $lead->getId()]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update Deadline');
?>
<div class="lead-deadline">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="lead-deadline-form">

		<?php $form = ActiveForm::begin([
			'id' => 'lead-deadline-form',
		]); ?>

		<div class="row">
			<?= $form->field($model, 'deadlineAt', [
				'options' => [
					'class' => 'col-md-3 col-lg-2',
				],
			])->widget(DateWidget::class) ?>

		</div>


		<div class="form-group">
			<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>

</div>

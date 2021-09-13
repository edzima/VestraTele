<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\models\IssueStageChangeForm;
use common\helpers\Html;
use common\widgets\ActiveForm;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\widgets\DetailView;

/* @var $this \yii\web\View */
/* @var $model IssueStageChangeForm */

$this->title = Yii::t('backend', 'Change Stage: {issue}', [
	'issue' => $model->getIssue()->getIssueName(),
]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = Yii::t('backend', 'Change Stage');

?>

<div class="issue-stage-view">

	<div class="row">
		<div class="col-md-6">
			<?= DetailView::widget([
				'model' => $model->getIssue(),
				'attributes' => [
					'type',
					'stage',
				],
			]) ?>

		</div>
	</div>
	<div class="row">
		<div class="issue-stage-form col-md-6">

			<?php $form = ActiveForm::begin(
				['id' => 'issue-stage-form']
			); ?>
			<div class="row">
				<?= $form->field($model, 'stage_id', [
					'options' => [
						'class' => 'col-md-8',
					],
				])
					->widget(Select2::class, [
						'data' => $model->getStagesData(),
					])
				?>

				<?= $form->field($model, 'date_at', [
					'options' => [
						'class' => 'col-md-4',
					],
				])
					->widget(DateTimeWidget::class)
				?>

			</div>

			<?= $form->field($model, 'description')->textarea() ?>


			<div class="form-group">
				<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
			</div>

			<?php ActiveForm::end(); ?>
		</div>


	</div>


</div>

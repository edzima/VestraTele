<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Url;
use backend\modules\issue\models\IssueStageChangeForm;
use common\helpers\Html;
use common\widgets\ActiveForm;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\web\JsExpression;
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
					->widget(DateTimeWidget::class, [
						'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
					])
				?>

			</div>

			<?= $form->field($model, 'description', [
				'options' => [
					'class' => 'select-text-area-field',
				],
			])->widget(Select2::class, [
				'options' => [
					'placeholder' => Yii::t('issue', 'Search for a description ...'),
					'class' => 'select-text-area',
				],
				'pluginEvents' => [
					'select2:open' => new JsExpression('function(e){
				let searchInput = document.getElementsByClassName("select2-search__field")[0];
				searchInput.value = e.currentTarget.value;
			}'),
				],
				'pluginOptions' => [
					'allowClear' => true,
					'minimumInputLength' => 3,
					'tags' => true,
					'ajax' => [
						'delay' => 250,
						'url' => Url::to(['/issue/note/description-list']),
						'dataType' => 'json',
						'data' => new JsExpression('function(params) { return {q:params.term}; }'),
						'templateResult' => new JsExpression('function(note) { return note.text; }'),
						'templateSelection' => new JsExpression('function (note) { return note.text; }'),
					],
				],
			])
			?>

			<div class="form-group">
				<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
			</div>

			<?php ActiveForm::end(); ?>
		</div>


	</div>


</div>


<style>
	.select-text-area-field .select2-selection--single {
		height: 102px;
	}

	.select-text-area-field .select2-selection--single .select2-selection__arrow {
		height: 100%;
	}

	.select-text-area-field .select2-selection--single .select2-selection__rendered {
		white-space: normal;
	}
</style>

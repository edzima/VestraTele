<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TaskStatus */
/* @var $task common\models\Task */
/* @var $form yii\widgets\ActiveForm */
/* @var $answers \common\models\AnswerTyp[] */

?>
	<div class="task-status-form">
		<?php
		if (!$model->isNewRecord) {
			echo Html::a('Usuń', ['delete', 'id' => $model->task_id], [
				'class' => 'btn btn-danger',
				'data' => [
					'confirm' => Yii::t('common', "confirm delete this"),
					'method' => 'post',
				],
			]);
		}
		?>

		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'answer_id', ['options' => ['class' => 'col-md-6']])->dropDownList($answers, ['prompt' => '']) ?>

		<?= $form->field($model, 'count_agreement', ['options' => ['class' => 'col-md-6']])->textInput(['type' => 'number', 'min' => 0])->label('Ilość podpisanych umów') ?>


		<?= $form->field($model, 'status_details')->textarea(['rows' => 6]) ?>

		<?= $form->field($model, 'name')->textArea(['maxlength' => true, 'rows' => 2]) ?>

		<div id="extra_agreement">
			<h4> Extra raport </h4>
			<?= $form->field($model, 'finished', ['options' => ['class' => 'col-md-6']])->checkbox() ?>

			<?= $form->field($model, 'extra_agreement', ['options' => ['class' => 'col-md-6']])->textInput(['type' => 'number', 'min' => 0]) ?>

			<?= $form->field($model, 'extra_name')->textarea(['rows' => 2, 'value' => null])->label('Kto do dopisania') ?>
		</div>
		<div class="form-group">
			<?= Html::submitButton('Zapisz', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>
	<h2>Szczegóły spotkania</h2>
<?= DetailView::widget([
	'model' => $task,
	'attributes' => [
		//'id',
		//'tele_id',
		[
			'attribute' => 'tele_id',
			'value' => $task->tele->username,
		],
		[
			'attribute' => 'agent_id',
			'value' => $task->agent->username,
		],

		'victim_name',
		'phone',
		[
			'attribute' => 'accident_id',
			'value' => $task->accident->name,
		],
		[
			'attribute' => 'woj',
			'value' => $task->wojewodztwo->name,
		],
		[
			'attribute' => 'powiat',
			'value' => $task->powiatRel->name,
		],
		[
			'attribute' => 'gmina',
			'value' => @$task->gminaRel->name,
		],
		[
			'attribute' => 'city',
			'value' => $task->miasto->name,
		],
		'qualified_name',
		'details:ntext',
		'meeting:boolean',
		'automat:boolean',
		'meeting:boolean',
		'date',
	],
]) ?>


<?php
$this->registerJs(
	'$("document").ready(function(){
			
			var extra = $("#extra_agreement");
			var answer = $("#taskstatus-answer_id");
			
			function showExtra(){
				if(answer.prop("value")==10) extra.show();
				else extra.hide();
			}
			
			showExtra();
			answer.on("change",function(event){
				showExtra();
			});
			
		});'
);

?>
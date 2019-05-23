<?php

use common\models\User;
use common\models\AnswerTyp;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;

$isTele = Yii::$app->user->can(User::ROLE_TELEMARKETER);

/* @var $this yii\web\View */
/* @var $model common\models\Task */

$this->title = 'Raport sprawy nr: ' . $model->task_id;

$this->params['breadcrumbs'][] = ['label' => 'Spotkania', 'url' => ['/spotkania']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-create">

	<h1><?= Html::encode($this->title) ?></h1>
	<div class="task-status-form">

		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'answer_id', ['options' => ['class' => 'col-md-6']])->dropDownList(ArrayHelper::map(AnswerTyp::find()->all(), 'id', 'name'), ['disabled' => $isTele]) ?>

		<?= $form->field($model, 'count_agreement', ['options' => ['class' => 'col-md-6']])->textInput(['type' => 'number', 'min' => 0, 'disabled' => $isTele])->label('Ilość podpisanych umów') ?>

		<?= $form->field($model, 'status_details')->textarea(['rows' => 6]) ?>

		<?= $form->field($model, 'name')->textArea(['maxlength' => true, 'rows' => 2, 'disabled' => $isTele]) ?>

		<div id="extra_agreement">
			<h4> Extra raport </h4>
			<?= $form->field($model, 'finished', ['options' => ['class' => 'col-md-6']])->checkbox(['disabled' => $isTele]) ?>

			<?= $form->field($model, 'extra_agreement', ['options' => ['class' => 'col-md-6']])->textInput(['type' => 'number', 'min' => 0, 'disabled' => $isTele]) ?>

			<?= $form->field($model, 'extra_name')->textarea(['rows' => 2, 'value' => null, 'disabled' => $isTele])->label('Kto do dopisania') ?>
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
				'value' => $task->gminaRel->name,
			],
			[
				'attribute' => 'city',
				'value' => $task->miasto->name,
			],
			'qualified_name',
			'details:ntext',
			'meeting:boolean',
			'automat:boolean',
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

</div>

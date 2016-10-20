<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\jui\Spinner;

/* @var $this yii\web\View */
/* @var $model common\models\TaskStatus */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="task-status-form">

    <?php $form = ActiveForm::begin(); ?>
	
	<?= $form->field($model, 'answer_id',['options'=>['class'=>'col-md-6']])->dropDownList($answers)?>
	
	<?=$form->field($model, 'count_agreement',['options'=>['class'=>'col-md-6']])->textInput(['type' => 'number', 'min'=>0])->label('Ilość podpisanych umów')?>
	


    <?= $form->field($model, 'status_details')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'name')->textArea(['maxlength' => true, 'rows'=>2]) ?>
	
	<div id="extra_agreement">
		<h4> Extra raport </h2>
		<?= $form->field($model, 'finished',['options'=>['class'=>'col-md-6']])->checkbox() ?>
		
		<?=$form->field($model, 'extra_agreement',['options'=>['class'=>'col-md-6']])->textInput(['type' => 'number', 'min'=>0])?>
		
		<?= $form->field($model, 'extra_name')->textarea(['rows' => 2, 'value'=>null])->label('Kto do dopisania') ?>
	</div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Zapisz' : 'Zapisz', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
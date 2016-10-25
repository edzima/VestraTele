<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\jui\Spinner;

use trntv\yii\datetime\DateTimeWidget;
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
		
		<?=$form->field($model, 'extra_agreement')->textInput(['type' => 'number', 'min'=>0])?>
		
		<?= $form->field($model, 'extra_name')->textarea(['rows' => 2, 'value'=>null])->label('Kto do dopisania') ?>
	</div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Zapisz' : 'Zapisz', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>



<h2>Szczegóły spotkania</h2>
	<div class="task-form">
    <?php $form = ActiveForm::begin(); ?>
	
		<?= $form->field($task, 'date',
		[	
			'options'=>['class'=>'col-md-4 form-group'],
			'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i> Data spotkania</span>{input}</div>'
		])
		->widget(DateTimeWidget::className(),
			[   'phpDatetimeFormat' => 'dd-MM-yyyy HH:mm',
				'clientOptions' => [
			
					'allowInputToggle' => true,
					'sideBySide' => true,
					'widgetPositioning' => [
					   'horizontal' => 'auto',
					   'vertical' => 'auto'
					],
				]
			]) 
	?>
	


        <?= Html::submitButton('Zmień', ['class' => 'btn btn-primary']) ?>
  

    <?php ActiveForm::end(); ?>
	</div>		
	 <?= DetailView::widget([
			'model' => $task,
			'attributes' => [
				//'id',
				//'tele_id',
				'date',
				[
					 'attribute' => 'accident_id',
					 'value' => $task->accident->name,
				],
				'qualified_name',
				'details:ntext',
				'victim_name',
				'phone',
			
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
				'city_code',
				'meeting:boolean',
				'automat:boolean',
			
				[
					 'attribute' => 'tele_id',
					 'value' => $task->tele->username,
				],
			],
		]) ?>


		<?php
	$this->registerJs(
		'$("document").ready(function(){
			
			var extra = $("#extra_agreement");
			var answer = $("#taskstatus-answer_id");
			
			function showExtra(){
				if(answer.prop("value")==10) extra.show("bind");
				else extra.hide("drop");
			}
			
			showExtra();
			answer.on("change",function(event){
				showExtra();
			});
			
		});'		
	);

?>
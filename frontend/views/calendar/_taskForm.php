<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;


use yii\bootstrap\Modal;
use trntv\yii\datetime\DateTimeWidget;

use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model common\models\Task */
/* @var $woj common\models\Task */
/* @var $accident common\models\Task */
/* @var $form yii\widgets\ActiveForm */
?>
<div id="taskModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 id="modalTitle" class="modal-title">Dodaj nowe spotkanie</h4>
            </div>
            <div id="modalBody" class="modal-body">

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'victim_name',
                    [
                        'options'=>['class' => 'col-md-6'],
                        'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-blind fa-lg"></i> Poszkodowany</span>{input}</div>'

                    ])->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'phone',
                    [
                        'options'=>['class'=>'col-md-6 form-group'],
                        'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-phone"></i> Numer</span>{input}</div>'
                    ])
                        ->widget(\yii\widgets\MaskedInput::className(), [
                            'mask' => '999-999-9999',
                        ])
                ?>

                <?= $form->field($model, 'qualified_name',
                    [
                        'options'=>['class' => 'col-md-12'],
                    ])
                    ->textArea(['maxlength' => true,'rows'=>3]) ?>




                <?= $form->field($model, 'agent_id',
                    [
                        'options'=>['class'=>'col-md-6 form-group'],
                        'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-user-secret"></i> Przedstawiciel</span>{input}</div>'
                    ])
                    ->dropDownList($agent)?>

                <?= $form->field($model, 'date',
                    [
                        'options'=>['class'=>'col-md-6 form-group '],
                        'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i> Kiedy</span>{input}</div>',

                    ])
                    ->widget(DateTimeWidget::className(),
                        [   'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm',
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



            	<?= $form->field($model, 'details',
            		[
                        'options'=>['class'=>'col-md-12 form-group'],
            			'template' => '<div class="input-group">{input}<span class="input-group-addon"><i class="fa fa-pencil-square-o "></i> Szczegóły</span></div>'
            		])->textArea(['rows'=>4]) ?>


            	<?= $form->field($model, 'accident_id',['options'=>['class'=>'col-md-6 form-group']])->dropDownList($accident)?>

            	<?=  $form->field($model, 'meeting',['options'=>['class'=>'col-md-3 form-group']])->dropDownList([0=>'Nie', 1=>'Tak'])?>

            	<?=  $form->field($model, 'automat',['options'=>['class'=>'col-md-3 form-group']])->dropDownList([0=>'Nie', 1=>'Tak'])?>
                <div class="clearfix"></div>
            	<h3>Adres</h3>

            	<?php
            	//wojewodztwo
            	echo $form->field($model, 'woj',['options'=>['class'=>'col-md-4 form-group']])->widget(Select2::classname(), [
            			'data' => $woj,
            			'options' => [
            				'placeholder' => '--Wybierz województwo--',
            				'id' => 'cat-id',
            			],
            		]
            	)->label(false);

            	//powiat
            	echo $form->field($model, 'powiat',
            		[
            			'options'=>['class'=>'col-md-4 form-group'],
            			'template' => '<div class="input-group">{input} <span id="add-powiat" class="input-group-addon add-terc"><i class="fa fa-plus"></i></span></div>'
            		])->widget(DepDrop::classname(), [
            		'type'=>DepDrop::TYPE_SELECT2,
            		'data'=>$powiat,
            		'options'=>['id'=>'subcat-id'],
            		'pluginOptions'=>[
            			'depends'=>['cat-id'],
            			'placeholder'=>'Powiat...',
            			'url'=>Url::to(['/city/powiat']),
            			'loading' =>'wyszukiwanie...'
            		]
            	]);

            	// gmina
            	echo $form->field($model, 'gmina',['options'=>['class'=>'col-md-4 form-group']])->widget(DepDrop::classname(), [
            		'type'=>DepDrop::TYPE_SELECT2,
            		'data'=>$gmina,
            		'pluginOptions'=>[
            			'depends'=>['cat-id', 'subcat-id'],
            			'placeholder'=>'Gmina...',
            			'url'=>Url::to(['/city/gmina']),
            		]
            	])->label(false);
                ?>

                <div class="clearfix"></div>

                <?php
            	// miasto
            	echo $form->field($model, 'city',
            		[
            			'options'=>['class'=>'col-md-6 form-group'],
            			'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-home"></i></span>{input} <span id="add-city" class="input-group-addon add-terc"><i class="fa fa-plus"></i></span></div>'
            		])
            		->widget(DepDrop::classname(), [
            		'type'=>DepDrop::TYPE_SELECT2,
            		'data'=>$city,
            		'pluginOptions'=>[
            			'depends'=>['cat-id', 'subcat-id'],
            			'placeholder'=>'Miejscowość...',
            			'url'=>Url::to(['/city/city']),
            		]
            	]);

            		echo  $form->field($model, 'street',
            		[
            			'options'=>['class'=>'col-md-4 form-group'],
            			'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-address-book"></i> Ulica</span>{input}</div>'
            		]);
            	?>


            	<?= $form->field($model, 'city_code',
            		[
            			'options'=>['class'=>'col-md-2 form-group'],
            			'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-barcode"></i> Kod</span>{input}</div>'
            		])
            		->widget(\yii\widgets\MaskedInput::className(), [
            			'mask' => '99-999',
            		])
            	?>

                </div>
                <div class="clearfix"></div>
                <div class="form-group clearfix text-center">
                    <?= Html::submitButton($model->isNewRecord ? 'Dodaj' : 'Aktualizuj', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
                 <?php ActiveForm::end(); ?>


             </div>
         </div>
     </div>

<?php
	$this->registerJs(
		'$("document").ready(function(){

			$("#add-city").click(function(){
				 window.open("/city/create");
			});
			$("#add-powiat").click(function(){
				 window.open("/powiat/create");
			});


		});'
	);

	$this->registerJs(
	"	var dateC = '$model->date'.substr(0,16);
		$('document').ready(function(){
			$('#task-date').val(dateC);
		});
	"
	);

?>

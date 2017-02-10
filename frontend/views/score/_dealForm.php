<?php

use yii\helpers\Html;

use yii\widgets\ActiveForm;
use trntv\yii\datetime\DateTimeWidget;
/* @var $this yii\web\View */
/* @var $model common\models\Score */

$this->title = 'Punkty';
$this->params['breadcrumbs'][] = ['label' => 'Ranking', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="score-create">



    <div class="score-form">

    <?php $form = ActiveForm::begin(); ?>
	<h3>Szczegóły: </h3>
	<?= $form->field($task, 'victim_name',['options'=>['class'=>'col-md-6 form-group']])->textInput(['disabled' =>true]) ?>
	<?= $form->field($task, 'meeting',['options'=>['class'=>'col-md-3 form-group']])->dropDownList([0=>'Tak', 1=>'Nie'],['disabled' =>true]) ?>
	<?= $form->field($task, 'automat',['options'=>['class'=>'col-md-3 form-group']])->dropDownList([0=>'Tak', 1=>'Nie'],['disabled' =>true]) ?>
    <h1><?= Html::encode($this->title) ?></h1>
	<?php
    foreach ($scores as $index => $score) {
		echo $form->field($score, "[$index]connexion", 
		[
			'options'=>['class'=>'col-md-2'],
			'template' => '<div class="input-group form-group"><span class="input-group-addon"><i class="fa fa-users"></i></span>{input}</div>'
		])->dropDownList($connexion,['disabled' =>true]);
		echo $form->field($score, "[$index]score",
		[
			'options'=>['class'=>'col-md-2'],
			'template' => '<div class="input-group form-group"><span class="input-group-addon"><i class="fa fa-dot-circle-o"></i></span>{input}</div>'
		])->textInput(['disabled' =>true]);
		echo $form->field($score, "[$index]name",
		[
			'options'=>['class'=>'col-md-5'],
			'template' => '<div class="input-group form-group"><span class="input-group-addon"><i class="fa fa-user"></i>Upoważniony/a</span>{input}</div>'
		])->textInput(['disabled' =>true]);
		echo $form->field($score,"[$index]date",
		[	
			'options'=>['class'=>'col-md-3'],
			'template' => '<div class="input-group form-group"><span class="input-group-addon"><i class="fa fa-calendar"></i> Kiedy</span>{input}</div>']
		)->widget(
			DateTimeWidget::className(),
			[   'phpDatetimeFormat' => 'yyyy-MM-dd',
				'clientOptions' => [
					'allowInputToggle' => true,
					'sideBySide' => true,
					'widgetPositioning' => [
					   'horizontal' => 'auto',
					   'vertical' => 'auto'
					],
				]
			]
		, ['disabled' =>true]);
	}
	?>

    <?php ActiveForm::end(); ?>

</div>

</div>

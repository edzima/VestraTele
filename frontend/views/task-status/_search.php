<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TaskStatusSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-status-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
	     'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

	<?=  $form->field($model, 'taskstatus',['options'=>['class'=>'col-md-3 form-group']])->dropDownList([''=>'',0=>'Nie', 1=>'Tak'])->label('Zaraportowane')?>
	<?=  $form->field($model, 'finish',['options'=>['class'=>'col-md-3 form-group']])->dropDownList([''=>'',0=>'Nie', 1=>'Tak'])->label('Zakończone')?>
	<?=  $form->field($model, 'meeting',['options'=>['class'=>'col-md-3 form-group']])->dropDownList([''=>'',0=>'Nie', 1=>'Tak'])->label('Umówione')?>
	
	<div class="form-group pull-right mt-25">
        <?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', ['index'], ['class' => 'btn btn-default'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

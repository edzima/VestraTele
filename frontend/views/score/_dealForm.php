<?php

use yii\helpers\Html;

use yii\widgets\ActiveForm;
use trntv\yii\datetime\DateTimeWidget;
/* @var $this yii\web\View */
/* @var $model common\models\Score */

$this->title = 'Rozdziel punkty';
$this->params['breadcrumbs'][] = ['label' => 'Scores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="score-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="score-form">

    <?php $form = ActiveForm::begin(); ?>

	<?php
    foreach ($scores as $index => $score) {
		echo $form->field($score, "[$index]connexion", ['options'=>['class'=>'col-md-6']])->dropDownList($connexion);
		echo $form->field($score, "[$index]score",['options'=>['class'=>'col-md-6']])->textInput(['type' => 'number', 'min'=>0]);
		echo $form->field($score, "[$index]name");
		echo $form->field($score,"[$index]date")->widget(
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
		);
	}
	?>

    <div class="form-group">
        <?= Html::submitButton('Zapisz') ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>

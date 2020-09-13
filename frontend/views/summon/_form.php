<?php

use common\models\issue\Issue;
use common\models\issue\Summon;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\issue\Summon */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="summon-form">

    <?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'status')->dropDownList(Summon::getStatusNames()) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

	<div class="row">

	<?= $form->field($model, 'created_at', [
		'options' => [
			'class' => 'col-md-3',
		],
	])
		->widget(DateTimeWidget::class, [
			'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm',
		]) ?>

	<?= $form->field($model, 'realized_at', [
		'options' => [
			'class' => 'col-md-3',
		],
	])
		->widget(DateTimeWidget::class, [
			'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm',
		]) ?>

	</div>
	<?= $form->field($model, 'issue_id')->dropDownList(Issue::find()->all()) ?>

    <?= $form->field($model, 'owner_id')->textInput() ?>

    <?= $form->field($model, 'contractor_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

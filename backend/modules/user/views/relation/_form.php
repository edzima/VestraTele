<?php

use common\models\user\UserRelation;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserRelation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-relation-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'user_id')->textInput() ?>

	<?= $form->field($model, 'to_user_id')->textInput() ?>

	<?= $form->field($model, 'type')->dropDownList(UserRelation::getTypesNames()) ?>
	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

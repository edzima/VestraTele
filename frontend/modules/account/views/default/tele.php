<?php

use common\models\user\UserProfile;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserProfile */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('frontend', 'Nowe spotkanie');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-default-settings">
	<h1><?= Html::encode($this->title) ?></h1>


	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'birthday')->widget(DateTimeWidget::class) ?>

	<?= $form->field($model, 'gender')->dropDownlist(UserProfile::getGendersNames(), ['prompt' => '']) ?>

	<?= $form->field($model, 'other')->textarea(['rows' => 6]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('frontend', 'Update'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end() ?>
</div>

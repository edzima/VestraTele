<?php

use common\models\user\LoginForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model LoginForm */

$this->title = Yii::t('frontend', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
	<h1><?= Html::encode($this->title) ?></h1>

	<p><?= Yii::t('frontend', 'Please fill out the following fields to login:') ?></p>

	<div class="row">
		<div class="col-lg-5">
			<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

			<?= $form->field($model, 'usernameOrEmail')->textInput(['autofocus' => true]) ?>

			<?= $form->field($model, 'password')->passwordInput() ?>

			<?= $form->field($model, 'rememberMe')->checkbox() ?>

			<div style="color:#999;margin:1em 0">
				<?= Yii::t('frontend', 'If you forgot your password you can')
				. ' ' . Html::a(Yii::t('frontend', 'reset it'), ['site/request-password-reset']) ?>.
				<br>
				<?= Yii::t('frontend', 'Need new verification email?') . ' '
				. Html::a(Yii::t('frontend', 'Resend'), ['site/resend-verification-email']) ?>
			</div>

			<div class="form-group">
				<?= Html::submitButton(Yii::t('frontend', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
			</div>

			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>

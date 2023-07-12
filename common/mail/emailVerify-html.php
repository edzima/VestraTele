<?php

/* @var $this yii\web\View */
/* @var $user common\models\user\User */

$this->title = Yii::t('common', 'Hello, {user}', [
	'user' => $user->username,
]);

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
$this->params['primaryButtonText'] = Yii::t('common', 'Verify');
$this->params['primaryButtonHref'] = $verifyLink;
?>
<div class="verify-email">
	<p><?= Yii::t('common', 'Follow the link below to verify your email:') ?></p>
</div>

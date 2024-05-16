<?php

use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $user common\models\user\User */

$this->title = Yii::t('common', 'Hello, {user}', [
	'user' => $user->username,
]);

$resetLink = FrontendUrl::toRoute(['/site/reset-password', 'token' => $user->password_reset_token], true);

$this->params['primaryButtonText'] = Yii::t('common', 'Reset');
$this->params['primaryButtonHref'] = $resetLink;

?>
<div class="password-reset">
	<p><?= Yii::t('common', 'Follow the link below to reset your password:') ?></p>
</div>

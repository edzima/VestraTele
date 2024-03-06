<?php

use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $user common\models\user\User */

$this->title = Yii::t('common', 'Hello, {user}', [
	'user' => $user->username,
]);

$verifyLink = FrontendUrl::toRoute(['/site/verify-email', 'token' => $user->verification_token], true);

$this->params['primaryButtonText'] = Yii::t('common', 'Verify');
$this->params['primaryButtonHref'] = $verifyLink;
?>
<div class="verify-email">
	<p><?= Yii::t('common', 'Follow the link below to verify your email:') ?></p>
</div>

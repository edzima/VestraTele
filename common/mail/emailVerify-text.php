<?php

use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $user common\models\user\User */

$verifyLink = FrontendUrl::toRoute(['/site/verify-email', 'token' => $user->verification_token], true);
?>
<?= Yii::t('common', 'Hello') ?> <?= $user->username ?>,

<?= Yii::t('common', 'Follow the link below to verify your email:') ?>

<?= $verifyLink ?>

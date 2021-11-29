<?php

/* @var $this yii\web\View */
/* @var $user common\models\user\User */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
<?= Yii::t('common', 'Hello') ?> <?= $user->username ?>,

<?= Yii::t('common', 'Follow the link below to verify your email:') ?>

<?= $verifyLink ?>

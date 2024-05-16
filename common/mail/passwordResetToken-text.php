<?php

use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $user common\models\user\User */

$resetLink = FrontendUrl::toRoute(['/site/reset-password', 'token' => $user->password_reset_token], true);
?>
<?= Yii::t('common', 'Hello') ?> <?= $user->username ?>,

<?= Yii::t('common', 'Follow the link below to reset your password:') ?>

<?= $resetLink ?>

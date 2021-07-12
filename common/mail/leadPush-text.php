<?php

use common\modules\lead\models\ActiveLead;

/* @var $this yii\web\View */
/* @var $lead ActiveLead $lead */

$leadLink = Yii::$app->urlManager->createAbsoluteUrl(['lead/lead/view', 'id' => $lead->getId()]);

?>
<?= Yii::t('lead', 'New Lead from: ' . $lead->getSource()->getName()) ?>
<?= Yii::t('lead', 'Type: ' . $lead->getSource()->getType()->getName()) ?>

<?= Yii::t('lead', 'Phone: {phone}', ['phone' => $lead->getPhone()]) ?>
<?= Yii::t('lead', 'Email: {email}', ['email' => $lead->getEmail()]) ?>

<?= $leadLink ?>

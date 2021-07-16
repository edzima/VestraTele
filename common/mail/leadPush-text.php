<?php

use common\modules\lead\models\ActiveLead;

/* @var $this yii\web\View */
/* @var $lead ActiveLead $lead */

$leadLink = Yii::$app->urlManager->createAbsoluteUrl(['lead/lead/view', 'id' => $lead->getId()]);
$reportLink = Yii::$app->urlManager->createAbsoluteUrl(['lead/report/report', 'id' => $lead->getId()]);

?>
<?= Yii::t('lead', 'Source: {name}', ['name' => $lead->getSource()->getType()->getName()]) ?>
<?= Yii::t('lead', 'Type: {name}', ['name' => $lead->getSource()->getType()->getName()]) ?>

<?= Yii::t('lead', 'Name: {name}', ['name' => $lead->getName()]) ?>
<?= $lead->getPhone() ? Yii::t('lead', 'Phone: {phone}', ['phone' => $lead->getPhone()]) : '' ?>
<?= $lead->getEmail() ? Yii::t('lead', 'Email: {email}', ['email' => $lead->getEmail()]) : '' ?>

<?= Yii::t('lead', 'Details') . ': ' . $leadLink ?>
<?= Yii::t('lead', 'Create Lead Report') . ': ' . $reportLink ?>

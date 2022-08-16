<?php

use common\modules\lead\models\ActiveLead;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $lead ActiveLead $lead */

$leadLink = FrontendUrl::leadView($lead->getId(), true);
$reportLink = FrontendUrl::toRoute(
	['/lead/report/report', 'id' => $lead->getId()],
	true
);
?>
<?= Yii::t('lead', 'Source: {name}', ['name' => $lead->getSource()->getType()->getName()]) ?>
<?= Yii::t('lead', 'Type: {name}', ['name' => $lead->getSource()->getType()->getName()]) ?>

<?= Yii::t('lead', 'Name: {name}', ['name' => $lead->getName()]) ?>
<?= $lead->getPhone() ? Yii::t('lead', 'Phone: {phone}', ['phone' => $lead->getPhone()]) : '' ?>
<?= $lead->getEmail() ? Yii::t('lead', 'Email: {email}', ['email' => $lead->getEmail()]) : '' ?>

<?= Yii::t('lead', 'Details') . ': ' . $leadLink ?>
<?= Yii::t('lead', 'Create Lead Report') . ': ' . $reportLink ?>

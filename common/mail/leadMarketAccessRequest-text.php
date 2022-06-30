<?php

use common\modules\lead\models\LeadMarketUser;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser */

$lead = $model->market->lead;
$leadLink = Yii::$app->urlManager->createAbsoluteUrl(['lead/lead/view', 'id' => $lead->getId()]);

?>

<?= Yii::t('lead', 'Name: {name}', ['name' => $lead->getName()]) ?>
<?= $lead->getPhone() ? Yii::t('lead', 'Phone: {phone}', ['phone' => $lead->getPhone()]) : '' ?>
<?= $lead->getEmail() ? Yii::t('lead', 'Email: {email}', ['email' => $lead->getEmail()]) : '' ?>

<?= Yii::t('lead', 'Details') . ': ' . $leadLink ?>

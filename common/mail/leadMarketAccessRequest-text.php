<?php

use common\modules\lead\models\LeadMarketUser;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser */

$lead = $model->market->lead;
$leadLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/lead/view', 'id' => $lead->getId()]);
$acceptLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/market-user/accept', 'market_id' => $model->market_id, 'user_id' => $model->user_id]);
$rejectLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/market-user/reject', 'market_id' => $model->market_id, 'user_id' => $model->user_id]);

?>

<?= Yii::t('lead', 'User: {userName} send request to Access Lead Market: {lead}', [
	'userName' => $model->user->getFullName(),
	'lead' => $lead->getName(),
]) ?>
<?= $lead->getPhone() ? Yii::t('lead', 'Phone: {phone}', ['phone' => $lead->getPhone()]) : '' ?>
<?= $lead->getEmail() ? Yii::t('lead', 'Email: {email}', ['email' => $lead->getEmail()]) : '' ?>

<?= Yii::t('lead', 'Details') . ': ' . $leadLink ?>

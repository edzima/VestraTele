<?php

use common\modules\lead\models\LeadMarketUser;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser */

$lead = $model->market->lead;

$leadLink = FrontendUrl::leadView($lead->getId());
$acceptLink = FrontendUrl::toRoute(['/lead/market-user/accept', 'market_id' => $model->market_id, 'user_id' => $model->user_id], true);
$rejectLink = FrontendUrl::toRoute(['/lead/market-user/reject', 'market_id' => $model->market_id, 'user_id' => $model->user_id], true);
?>

<?= Yii::t('lead', 'User: {userName} send request to Access Lead Market: {lead}', [
	'userName' => $model->user->getFullName(),
	'lead' => $lead->getName(),
]) ?>
<?= $lead->getPhone() ? Yii::t('lead', 'Phone: {phone}', ['phone' => $lead->getPhone()]) : '' ?>
<?= $lead->getEmail() ? Yii::t('lead', 'Email: {email}', ['email' => $lead->getEmail()]) : '' ?>

<?= Yii::t('lead', 'Details') . ': ' . $leadLink ?>

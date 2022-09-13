<?php

use common\modules\lead\models\LeadMarketUser;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser */

$lead = $model->market->lead;

$leadLink = FrontendUrl::leadView($lead->getId(), true);
$acceptLink = FrontendUrl::toRoute(['/lead/market-user/accept', 'market_id' => $model->market_id, 'user_id' => $model->user_id], true);
$rejectLink = FrontendUrl::toRoute(['/lead/market-user/reject', 'market_id' => $model->market_id, 'user_id' => $model->user_id], true);
?>

<?= Yii::t('lead', 'User: {userName} send request to Access Lead Market: {lead}', [
	'userName' => $model->user->getFullName(),
	'lead' => $lead->getName(),
]) ?>

<?= Yii::t('lead', 'Status Market') ?>: <?= $model->market->getStatusName() ?>

<?= Yii::t('lead', 'Details') . ': ' . $model->details ?>

<?= Yii::t('lead', 'Accept') . ': ' . $acceptLink ?>

<?= Yii::t('lead', 'Reject') . ': ' . $rejectLink ?>


<?= Yii::t('lead', 'Status Lead') ?>: <?= $lead->getStatusName() ?>

<?= Yii::t('lead', 'Type Lead') ?>: <?= $lead->getTypeName() ?>

<?= $lead->getName() . ': ' . $leadLink ?>

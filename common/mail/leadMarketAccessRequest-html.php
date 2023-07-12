<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarketUser;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser $lead */

$lead = $model->market->lead;
$leadLink = FrontendUrl::leadView($lead->getId(), true);
$acceptLink = FrontendUrl::toRoute(['/lead/market-user/accept', 'market_id' => $model->market_id, 'user_id' => $model->user_id], true);
$rejectLink = FrontendUrl::toRoute(['/lead/market-user/reject', 'market_id' => $model->market_id, 'user_id' => $model->user_id], true);

$this->title = Yii::t('lead', 'User: {userName} send request to Access Lead Market: {lead}', [
	'userName' => $model->user->getFullName(),
	'lead' => $lead->getName(),
]);

$this->params['primaryButtonText'] = Yii::t('lead', 'Lead');
$this->params['primaryButtonHref'] = $leadLink;

?>
<div class="lead-market-access-request-email">

	<?= Yii::t('lead', 'Status Market') ?>: <?= Html::encode($model->market->getStatusName()) ?>

	<?= empty($model->details)
		? ''
		: Yii::t('lead', 'Details') . ': ' . Html::encode($model->details)
	?>

	<p><?= Html::a(Yii::t('lead', 'Accept'), $acceptLink) ?></p>
	<p><?= Html::a(Yii::t('lead', 'Reject'), $rejectLink) ?></p>


	<p><?= Yii::t('lead', 'Status Lead') ?>: <?= Html::encode($lead->getStatusName()) ?></p>

	<p><?= Yii::t('lead', 'Type Lead') ?>: <?= Html::encode($lead->getTypeName()) ?></p>

</div>

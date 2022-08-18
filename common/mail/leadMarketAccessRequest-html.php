<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarketUser;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser $lead */

$lead = $model->market->lead;

$leadLink = FrontendUrl::toRoute($lead->getId(), true);
$acceptLink = FrontendUrl::toRoute(['/lead/market-user/accept', 'market_id' => $model->market_id, 'user_id' => $model->user_id], true);
$rejectLink = FrontendUrl::toRoute(['/lead/market-user/reject', 'market_id' => $model->market_id, 'user_id' => $model->user_id], true);

?>
<div class="lead-market-access-request-email">
	<p><?= Yii::t('lead', 'User: {userName} send request to Access Lead Market: {lead}', [
			'userName' => $model->user->getFullName(),
			'lead' => $lead->getName(),
		]) ?>
	</p>

	<p>
		<?= Yii::t('lead', 'Details') . ': ' . Html::encode($model->details) ?>
	</p>


	<p><?= Html::a(Yii::t('lead', 'Accept'), $acceptLink) ?></p>
	<p><?= Html::a(Yii::t('lead', 'Reject'), $rejectLink) ?></p>

	<p><?= Yii::t('lead', 'Status') ?>: <?= Html::encode($model->market->getStatusName()) ?></p>

	<p><?= Yii::t('lead', 'Status Lead') ?>: <?= Html::encode($lead->getStatusName()) ?></p>

	<p><?= Yii::t('lead', 'Type Lead') ?>: <?= Html::encode($lead->getTypeName()) ?></p>

	<p><?= Html::a(Html::encode($leadLink), $leadLink) ?></p>


</div>

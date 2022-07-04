<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarketUser;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser $lead */

$lead = $model->market->lead;
$leadLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/lead/view', 'id' => $lead->getId()]);
$acceptLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/market-user/accept', 'market_id' => $model->market_id, 'user_id' => $model->user_id]);
$rejectLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/market-user/reject', 'market_id' => $model->market_id, 'user_id' => $model->user_id]);

?>
<div class="lead-market-access-request-email">
	<p><?= Yii::t('lead', 'User: {userName} send request to Access Lead Market: {lead}', [
			'userName' => $model->user->getFullName(),
			'lead' => $lead->getName(),
		]) ?>
	</p>

	<p><?= Html::a(Yii::t('lead', 'Accept'), $acceptLink) ?></p>
	<p><?= Html::a(Yii::t('lead', 'Reject'), $rejectLink) ?></p>

	<p><?= Yii::t('lead', 'Status') ?>: <?= $model->market->getStatusName() ?></p>

	<p><?= Html::a(Html::encode($leadLink), $leadLink) ?></p>


</div>

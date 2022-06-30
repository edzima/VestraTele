<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarketUser;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser $lead */

$lead = $model->market->lead;
$leadLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/lead/view', 'id' => $lead->getId()]);

?>
<div class="lead-market-access-request-email">
	<p><?= Yii::t('lead', 'User: {userName} send request to Access Lead Market: {lead}', [
			'userName' => $model->user->getFullName(),
			'lead' => $lead->getName(),
		]) ?>
	</p>

	<p><?= Yii::t('lead', 'Status') ?>: <?= $model->market->getStatusName() ?></p>

	<p><?= Html::a(Html::encode($leadLink), $leadLink) ?></p>

</div>

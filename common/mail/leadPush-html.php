<?php

use common\helpers\Html;
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
<div class="lead-push-email">
	<p><?= Yii::t('lead', 'Source') ?>: <?= Html::encode($lead->getSource()->getName()) ?></p>
	<p><?= Yii::t('lead', 'Type') ?>: <?= Html::encode($lead->getSource()->getType()->getName()) ?></p>

	<p><?= Yii::t('lead', 'Phone') ?>: <?= Html::telLink($lead->getPhone()) ?></p>
	<p><?= Yii::t('lead', 'Email') ?>: <?= Html::mailto($lead->getEmail()) ?></p>

	<p><?= Html::a(Html::encode($leadLink), $leadLink) ?></p>
	<p><?= Html::a(Yii::t('lead', 'Create Lead Report'), $reportLink) ?></p>
</div>

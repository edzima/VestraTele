<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $lead ActiveLead $lead */

$this->title = Yii::t('lead', 'New Lead: {lead}', [
	'lead' => $lead->getName(),
]);

$reportLink = FrontendUrl::toRoute(
	[
		'/lead/report/report',
		'id' => $lead->getId(),
		'hash' => $lead->getHash(),
	],
	true
);

$this->params['primaryButtonText'] = Yii::t('lead', 'Create Lead Report');
$this->params['primaryButtonHref'] = $reportLink;
?>
<div class="lead-push-email">
	<p><?= Yii::t('lead', 'Source') ?>: <?= Html::encode($lead->getSource()->getName()) ?></p>
	<p><?= Yii::t('lead', 'Type') ?>: <?= Html::encode($lead->getSource()->getType()->getName()) ?></p>

	<p><?= Yii::t('lead', 'Phone') ?>: <?= Html::telLink($lead->getPhone()) ?></p>
	<p><?= Yii::t('lead', 'Email') ?>: <?= Html::mailto($lead->getEmail()) ?></p>
</div>

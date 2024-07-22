<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $lead ActiveLead $lead */

$this->title = Yii::t('lead', 'New Lead: {lead}', [
	'lead' => $lead->getName(),
]);

$leadLink = FrontendUrl::leadView($lead->getId(), true);

$this->params['primaryButtonText'] = Yii::t('lead', 'Details');
$this->params['primaryButtonHref'] = $leadLink;
?>
<div class="lead-push-email">
	<p><?= Yii::t('lead', 'Source') ?>: <?= Html::encode($lead->getSource()->getName()) ?></p>
	<p><?= Yii::t('lead', 'Type') ?>: <?= Html::encode($lead->getSource()->getType()->getName()) ?></p>

	<p><?= Yii::t('lead', 'Phone') ?>: <?= Html::telLink($lead->getPhone()) ?></p>
	<p><?= Yii::t('lead', 'Email') ?>: <?= Html::mailto($lead->getEmail()) ?></p>
</div>

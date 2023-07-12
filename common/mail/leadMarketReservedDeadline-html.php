<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarketUser;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser */

$this->title = Yii::t('lead', 'You will soon lose access to Lead.');
$lead = $model->market->lead;
$leadLink = FrontendUrl::leadView($lead->getId(), true);
$this->params['primaryButtonText'] = Yii::t('lead', 'Lead');
$this->params['primaryButtonHref'] = $leadLink;
?>
<div class="lead-market-reserved-deadline">
	<p><?= Yii::t('lead', 'Source') ?>: <?= Html::encode($lead->getSource()->getName()) ?></p>
	<p><?= Yii::t('lead', 'Type') ?>: <?= Html::encode($lead->getSource()->getType()->getName()) ?></p>
	<p><?= Yii::t('lead', 'Status') ?>: <?= Html::encode($lead->getStatusName()) ?></p>

	<p><?= Yii::t('lead', 'Phone') ?>: <?= Html::telLink($lead->getPhone()) ?></p>
	<p><?= Yii::t('lead', 'Email') ?>: <?= Html::mailto($lead->getEmail()) ?></p>
</div>


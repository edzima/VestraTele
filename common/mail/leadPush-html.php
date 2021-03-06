<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;

/* @var $this yii\web\View */
/* @var $lead ActiveLead $lead */

$leadLink = Yii::$app->urlManager->createAbsoluteUrl(['lead/lead/view', 'id' => $lead->getId()]);
?>
<div class="lead-push-email">
	<p><?= Yii::t('lead', 'Source') ?>: <?= Html::encode($lead->getSource()->getName()) ?></p>
	<p><?= Yii::t('lead', 'Type') ?>: <?= Html::encode($lead->getSource()->getType()->getName()) ?></p>

	<p><?= Yii::t('lead', 'Phone') ?>: <?= Html::telLink($lead->getPhone()) ?></p>
	<p><?= Yii::t('lead', 'Email') ?>: <?= Html::mailto($lead->getEmail()) ?></p>

	<p><?= Html::a(Yii::t('lead', 'View Details'), $leadLink) ?></p>
</div>

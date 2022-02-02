<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;

/* @var $this yii\web\View */
/* @var $lead ActiveLead $lead */

$leadLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/lead/view', 'id' => $lead->getId()]);
$reportLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/report/report', 'id' => $lead->getId()]);

?>
<div class="lead-push-email">
	<p><?= Yii::t('lead', 'Source') ?>: <?= Html::encode($lead->getSource()->getName()) ?></p>
	<p><?= Yii::t('lead', 'Type') ?>: <?= Html::encode($lead->getSource()->getType()->getName()) ?></p>

	<p><?= Yii::t('lead', 'Phone') ?>: <?= Html::telLink($lead->getPhone()) ?></p>
	<p><?= Yii::t('lead', 'Email') ?>: <?= Html::mailto($lead->getEmail()) ?></p>

	<p><?= Html::a(Html::encode($leadLink), $leadLink) ?></p>
	<p><?= Html::a(Yii::t('lead', 'Create Lead Report'), $reportLink) ?></p>

</div>

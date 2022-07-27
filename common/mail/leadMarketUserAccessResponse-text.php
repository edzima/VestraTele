<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarketUser;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser $lead */

?>

<?php if ($model->isAccepted()): ?>
	<?php
	$lead = $model->market->lead;
	$leadLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/lead/view', 'id' => $lead->getId()]);
	?>
	<?= Yii::t('lead', 'Your Access Request for Lead: {lead} from Market is Accepted.', [
		'lead' => $lead->getName(),
	]) ?>
	<?= $model->getAttributeLabel('reserved_at') . ': ' . Yii::$app->formatter->asDate($model->reserved_at) ?>
	<?= Html::a(Html::encode($leadLink), $leadLink) ?>
<?php elseif ($model->isRejected()): ?>
	<?= Yii::t('lead', 'Your Access Request for Lead: {lead} from Market is rejected.', [
		'lead' => $model->market->lead->getName(),
	]) ?>
	<?= Yii::getAlias('@frontendUrl')
	. Yii::$app->urlManager->createUrl(['lead/market-user/access-request', 'id' => $model->market_id])
	?>

<?php endif; ?>


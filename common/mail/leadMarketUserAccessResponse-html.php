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
	<h1>
		<?= Yii::t('lead', 'Your Access Request for Lead: {lead} from Market is Accepted.', [
			'lead' => $lead->getName(),
		]) ?>
	</h1>
	<p>
		<?= $model->getAttributeLabel('reserved_at') . ': ' . Yii::$app->formatter->asDate($model->reserved_at) ?>
	</p>
	<p><?= Html::a(Html::encode($leadLink), $leadLink) ?></p>
<?php elseif ($model->isRejected()): ?>
	<h1>
		<?= Yii::t('lead', 'Your Access Request for Lead: {lead} from Market is rejected.', [
			'lead' => $model->market->lead->getName(),
		]) ?>
	</h1>
	<?= Html::a(Yii::t('lead', 'Request Access'),
		Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/market-user/access-request', 'id' => $model->market_id])
	) ?>

<?php endif; ?>


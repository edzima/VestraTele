<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarketUser;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser $lead */

?>

<?php if ($model->isAccepted()): ?>
	<?php
	$lead = $model->market->lead;
	$leadLink = FrontendUrl::leadView($lead->getId(), true);
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
		FrontendUrl::toRoute(
			['/lead/market-user/access-request', 'id' => $model->market_id],
			true
		)
	) ?>


<?php elseif ($model->isWaiting()): ?>
	<h1>
		<?= Yii::t('lead', 'Your Access Request for Lead: {lead} from Market is Waiting.', ['lead' => $model->market->lead->getName()]) ?>
	</h1>

<?php endif; ?>



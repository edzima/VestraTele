<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarketUser;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser $lead */

if ($model->isAccepted()) {
	$lead = $model->market->lead;

	$this->title = Yii::t('lead', 'Your Access Request for Lead: {lead} from Market is Accepted.', [
		'lead' => $lead->getName(),
	]);

	$this->params['primaryButtonText'] = Yii::t('lead', 'Lead');
	$this->params['primaryButtonHref'] = FrontendUrl::leadView($lead->getId(), true);

	echo $model->getAttributeLabel('reserved_at') . ': ' . Yii::$app->formatter->asDate($model->reserved_at);
} elseif ($model->isRejected()) {
	$this->title = Yii::t('lead', 'Your Access Request for Lead: {lead} from Market is rejected.', [
		'lead' => $model->market->lead->getName(),
	]);
	$this->params['primaryButtonText'] = Yii::t('lead', 'Request Access');
	$this->params['primaryButtonHref'] = FrontendUrl::toRoute(
		['/lead/market-user/access-request', 'id' => $model->market_id],
		true
	);
} elseif ($model->isWaiting()) {
	$this->title = Yii::t('lead', 'Your Access Request for Lead: {lead} from Market is Waiting.', ['lead' => $model->market->lead->getName()]);
}

?>

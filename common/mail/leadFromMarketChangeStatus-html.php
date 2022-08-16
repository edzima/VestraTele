<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarket;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarket */

$lead = $model->lead;
$leadLink = FrontendUrl::leadView($lead->getId(), true);

?>
<div class="lead-from-market-change-status">
	<p><?= Yii::t('lead', 'Lead: {lead} from Market has changed Status: {status}.', [
			'lead' => $lead->getName(),
			'status' => $lead->getStatusName(),
		]) ?>
	</p>

	<p><?= Yii::t('lead', 'Market Status: {status}.', [
			'status' => $model->getStatusName(),
		]) ?>
	</p>

	<p><?= Html::a(Html::encode($lead->getName()), $leadLink) ?></p>

</div>

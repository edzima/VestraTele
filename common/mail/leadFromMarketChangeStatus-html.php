<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarket;

/* @var $this yii\web\View */
/* @var $model LeadMarket */

$lead = $model->lead;
$leadLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/lead/view', 'id' => $lead->getId()]);

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

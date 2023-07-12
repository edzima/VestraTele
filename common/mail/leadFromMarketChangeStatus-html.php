<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarket;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarket */

$lead = $model->lead;

$this->title = Yii::t('lead', 'Lead: {lead} from Market change Status: {status}.', [
	'lead' => $lead->getName(),
	'status' => $lead->getStatusName(),
]);

$leadLink = FrontendUrl::leadView($lead->getId(), true);
$marketLink = FrontendUrl::toRoute(['/lead/market/view', 'id' => $model->id], true);

$this->params['primaryButtonText'] = Yii::t('lead', 'Lead');
$this->params['primaryButtonHref'] = $leadLink;
?>
<div class="lead-from-market-change-status">
	<p>
		<?= Yii::t('lead', 'Status Market') ?>: <?= Html::a($model->getStatusName(), $marketLink) ?>
	</p>
</div>

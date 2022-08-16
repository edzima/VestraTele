<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarketUser;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser */

$lead = $model->market->lead;
$leadLink = FrontendUrl::leadView($lead->getId(), true);

?>
<div class="lead-market-reserved-deadline-email">
	<p><?= Yii::t('lead', 'You will soon lose access to Lead.') ?></p>

	<p><?= Html::a(Html::encode($leadLink), $leadLink) ?></p>

</div>

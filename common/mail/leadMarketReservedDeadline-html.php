<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarketUser;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser */

$lead = $model->market->lead;
$leadLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/lead/view', 'id' => $lead->getId()]);

?>
<div class="lead-market-reserved-deadline-email">
	<p><?= Yii::t('lead', 'You will soon lose access to Lead.') ?></p>

	<p><?= Html::a(Html::encode($leadLink), $leadLink) ?></p>

</div>

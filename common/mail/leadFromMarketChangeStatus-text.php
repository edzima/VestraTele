<?php

use common\modules\lead\models\LeadMarket;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarket */

$lead = $model->lead;
$leadLink = FrontendUrl::leadView($lead->getId(), true);

?>
<?= Yii::t('lead', 'Lead: {lead} from Market has changed Status: {status}.', [
	'lead' => $lead->getName(),
	'status' => $lead->getStatusName(),
]) ?>

<?= Yii::t('lead', 'Market Status: {status}.', [
	'status' => $model->getStatusName(),
]) ?>


<?= $leadLink ?>

<?php

use common\modules\lead\models\LeadMarket;

/* @var $this yii\web\View */
/* @var $model LeadMarket */

$lead = $model->lead;
$leadLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/lead/view', 'id' => $lead->getId()]);

?>
<?= Yii::t('lead', 'Lead: {lead} from Market has changed Status: {status}.', [
	'lead' => $lead->getName(),
	'status' => $lead->getStatusName(),
]) ?>

<?= Yii::t('lead', 'Market Status: {status}.', [
	'status' => $model->getStatusName(),
]) ?>


<?= $leadLink ?>

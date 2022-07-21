<?php

use common\modules\lead\models\LeadMarketUser;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser */

$lead = $model->market->lead;
$leadLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/lead/view', 'id' => $lead->getId()]);

?>
<?= Yii::t('lead', 'You will soon lose access to Lead.') ?>
<?= $leadLink ?>


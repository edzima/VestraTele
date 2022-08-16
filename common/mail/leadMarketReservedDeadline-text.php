<?php

use common\modules\lead\models\LeadMarketUser;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser */

$lead = $model->market->lead;
$leadLink = FrontendUrl::leadView($lead->getId(), true);

?>
<?= Yii::t('lead', 'You will soon lose access to Lead.') ?>
<?= $leadLink ?>


<?php

use backend\helpers\Url;
use common\models\issue\event\IssueUserEvent;
use common\models\issue\IssueSettlement;

/* @var $this yii\web\View */
/* @var $event IssueUserEvent */
/* @var $settlement IssueSettlement */

$settlementLink = Url::settlementView($settlement->getId(), true);
$issueLink = Url::issueView($settlement->getIssueId(), true);

?>
<?= $event->getTranslateName() ?>

<?= Yii::t('provision', 'Settlement mark as Provision Control.') ?>

<?= $issueLink ?>



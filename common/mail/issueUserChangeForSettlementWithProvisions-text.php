<?php

use backend\helpers\Url;
use common\models\issue\IssueInterface;

/* @var $this yii\web\View */
/* @var $title string */
/* @var $issue IssueInterface */

$issueLink = Url::issueView($issue->getIssueId(), true);

?>
<?= $title ?>

<?= Yii::t('provision', 'Settlement mark as Provision Control.') ?>

<?= $issueLink ?>



<?php

use backend\helpers\Url;
use common\models\issue\event\IssueUserEvent;
use common\models\issue\IssueSettlement;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $event IssueUserEvent */
/* @var $settlement IssueSettlement */

$settlementLink = Url::settlementView($settlement->getId(), true);
$issueLink = Url::issueView($settlement->getIssueId(), true);

?>
<div class="issue-user-change-for-settlement-with-provisions">
	<p><?= $event->getTranslateName() ?></p>

	<p><?= Yii::t('provision', 'Settlement mark as Provision Control.') ?></p>

	<p><?= Html::a(Html::encode($settlement->getIssueName(), $issueLink)) ?></p>

	<p><?= Html::a(Html::encode($settlement->getTypeName()), $settlementLink) ?></p>
</div>

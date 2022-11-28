<?php

use backend\helpers\Url;
use common\models\issue\IssueInterface;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $title string */
/* @var $issue IssueInterface */

$issueLink = Url::issueView($issue->getIssueId(), true);

?>
<div class="issue-user-change-for-settlement-with-provisions">
	<h1><?= $title ?></h1>

	<p><?= Yii::t('provision', 'Settlement mark as Provision Control.') ?></p>

	<p><?= Html::a(Html::encode($issue->getIssueName()), $issueLink) ?></p>

</div>

<?php

use backend\helpers\Url;
use common\models\issue\IssueInterface;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $title string */
/* @var $issue IssueInterface */

$this->title = $title;

$issueLink = Url::issueView($issue->getIssueId(), true);

$this->params['primaryButtonText'] = $issue->getIssueName();
$this->params['primaryButtonHref'] = $issueLink;
?>
<div class="issue-user-change-for-settlement-with-provisions">
	<p><?= Yii::t('provision', 'Settlement mark as Provision Control.') ?></p>
</div>

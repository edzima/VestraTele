<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\models\IssueClaimForm;

/* @var $this yii\web\View */
/* @var $model IssueClaimForm */

$issue = $model->getIssue();
$this->title = Yii::t('issue', 'Create Claim: {issue}', [
	'issue' => $issue->getIssueName(),
]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Claims'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-claim-create">

	<?= $this->render('_issue_view', [
		'model' => $issue,
	]) ?>

	<?= $this->render('_form', [
		'model' => $model,
		'onlyField' => false,
	]) ?>

</div>

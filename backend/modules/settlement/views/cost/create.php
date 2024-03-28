<?php

use backend\modules\settlement\models\IssueCostForm;
use common\models\message\IssueCostMessagesForm;

/* @var $this yii\web\View */
/* @var $model IssueCostForm */
/* @var $message IssueCostMessagesForm */

$this->title = Yii::t('backend', 'Create cost');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['/issue/issue/index']];

if ($model->getIssue()) {
	$this->title = Yii::t('backend', 'Create cost: {issue}', ['issue' => $model->getIssue()->getIssueName()]);
	$this->params['breadcrumbs'][] = ['label' => $model->getIssue()->getIssueName(), 'url' => ['/issue/issue/view', 'id' => $model->getIssue()->getIssueId()]];
	$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Costs'), 'url' => ['issue', 'id' => $model->getIssue()->getIssueId()]];
}
$this->params['breadcrumbs'][] = Yii::t('backend', 'Create');
?>
<div class="issue-cost-create">

	<?= $this->render('_form', [
		'model' => $model,
		'message' => $message,
	]) ?>


</div>

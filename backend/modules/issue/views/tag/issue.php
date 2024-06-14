<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\models\IssueTagsLinkForm;
use common\models\issue\IssueInterface;
use common\models\user\Worker;
use yii\web\View;

/* @var $this View */
/* @var $model IssueTagsLinkForm */
/* @var $issue IssueInterface */

$this->title = Yii::t('issue', 'Issue: {issue} - Tags', [
	'issue' => $issue->getIssueName(),
]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
if (Yii::$app->user->can(Worker::PERMISSION_ISSUE_TAG_MANAGER)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Tags'), 'url' => ['index']];
}
?>
<div class="issue-tag-issue-tags-link">
	<?= $this->render('_link-form', [
		'model' => $model,
	]) ?>
</div>

<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\models\IssueForm;
use common\models\issue\Issue;
use common\models\message\IssueCreateMessagesForm;

/* @var $this yii\web\View */
/* @var $baseIssue Issue */
/* @var $model IssueForm */
/* @var $messagesModel IssueCreateMessagesForm */

$this->title = Yii::t('issue', 'Create & Link from Issue: {issue}', ['issue' => $baseIssue->getIssueName()]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($baseIssue);
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-create-and-link">

	<?= $this->render('_form', [
		'model' => $model,
		'messagesModel' => $messagesModel,
	]) ?>

</div>

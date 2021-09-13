<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\models\IssueNoteForm;
use common\models\issue\IssueInterface;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
/* @var $issue IssueInterface */

$this->title = Yii::t('issue', 'Create Issue Note for: {issue}', [
	'issue' => $issue->getIssueName(),
]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Notes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('issue', 'Create Issue Note');
?>
<div class="issue-note-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

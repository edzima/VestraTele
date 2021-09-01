<?php

use backend\modules\issue\models\IssueNoteForm;
use common\models\issue\IssueInterface;
use frontend\helpers\Breadcrumbs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
/* @var $issue IssueInterface */

$this->title = Yii::t('issue', 'Create Issue Note for: {issue}', ['issue' => $issue->getIssueName()]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-note-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

<?php

use backend\modules\issue\models\IssueTagsLinkForm;
use yii\web\View;

/* @var $this View */
/* @var $model IssueTagsLinkForm */

$this->title = Yii::t('issue', 'Link Tags to Issues: {count}', ['count' => count($model->issuesIds)]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="issue-tag-link-multiple">
	<?= $this->render('_link-form', [
		'model' => $model,
	]) ?>
</div>

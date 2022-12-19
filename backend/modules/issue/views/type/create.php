<?php

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueType */

$this->title = Yii::t('backend', 'Create Issue Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-type-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

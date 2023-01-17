<?php

use backend\modules\issue\models\IssueStageForm;

/* @var $this yii\web\View */
/* @var $model IssueStageForm */

$this->title = Yii::t('backend', 'Create Issue Stage');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Stages'), 'url' => ['stage/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-stage-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

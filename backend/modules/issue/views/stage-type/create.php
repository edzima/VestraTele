<?php

use backend\modules\issue\models\IssueStage;
use backend\modules\issue\models\StageTypeForm;
use common\models\issue\IssueType;

/* @var $this yii\web\View */
/* @var $model StageTypeForm */
/* @var $type IssueType|null */
/* @var $stage IssueStage|null */

if ($stage && $type) {
	$this->title = Yii::t('backend', 'Link: {stage} with Type: {type}', [
		'stage' => $stage->name,
		'type' => $type->name,
	]);
} else {
	if ($type !== null) {
		$this->title = Yii::t('backend', 'Link: {type} with Stage', [
			'type' => $type->name,
		]);
	} else {
		$this->title = Yii::t('backend', 'Link: {stage} with Type', [
			'stage' => $stage->name,
		]);
	}
}

$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
if ($type) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Types'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $type->name, 'url' => ['type/view', 'id' => $type->id]];
}
if ($stage) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Stages'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $stage->name, 'url' => ['stage/view', 'id' => $stage->id]];
}
$this->params['breadcrumbs'][] = Yii::t('backend', 'Link');
?>
<div class="issue-stage-type-create">

	<?= $this->render('_form', [
		'model' => $model,
		'withType' => $type === null,
		'withStage' => $stage === null,
	]) ?>

</div>

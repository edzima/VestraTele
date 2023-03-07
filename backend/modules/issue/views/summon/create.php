<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\models\SummonForm;

/* @var $this yii\web\View */
/* @var $model SummonForm */

$issue = $model->getIssue();

if ($issue === null) {
	$this->title = $model->getType()
		? Yii::t('backend', 'Create Summon: {type}', [
			'type' => $model->getType()->name,
		])
		: Yii::t('backend', 'Create Summon');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
} else {
	$this->title = $model->getType()
		? Yii::t('backend', 'Create Summon: {type} in Issue: {issue}', [
			'type' => $model->getType()->name,
			'issue' => $issue->getIssueName(),
		])
		: Yii::t('backend', 'Create Summon in Issue: {issue}', [
			'issue' => $issue->getIssueName(),
		]);
	$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
}

$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->getType()
	? Yii::t('backend', 'Create Summon: {type}', [
		'type' => $model->getType()->name,
	])
	: Yii::t('backend', 'Create Summon');
?>
<div class="summon-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>

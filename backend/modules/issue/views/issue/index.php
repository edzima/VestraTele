<?php

use backend\modules\issue\models\search\IssueSearch;
use common\behaviors\IssueTypeParentIdAction;
use common\widgets\grid\SelectionForm;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('backend', 'Issues');

if (!$searchModel->getIssueMainType()) {
	$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
} else {
	$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => IssueTypeParentIdAction::urlAll()];
	$this->params['breadcrumbs'][] = ['label' => $searchModel->getIssueMainType()->name];
}
$this->params['issueParentTypeNav'] = [
	'route' => ['/issue/issue/index'],
];
?>
<div class="issue-index">

	<?php Pjax::begin([
		'timeout' => 2000,
	]); ?>


	<?= $this->render('_top-buttons', [
		'parentTypeId' => $searchModel->parentTypeId,
	]) ?>

	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= $this->render('_grid-selection-buttons', [
		'searchModel' => $searchModel,
		'dataProvider' => $dataProvider,
		'gridId' => 'issues-list',
	]) ?>



	<?= $this->render('_grid', [
		'dataProvider' => $dataProvider,
		'searchModel' => $searchModel,
		'gridId' => 'issues-list',
	]) ?>

	<?php
	SelectionForm::end();
	Pjax::end();
	?>


</div>

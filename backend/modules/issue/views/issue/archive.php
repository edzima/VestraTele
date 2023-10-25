<?php

use backend\modules\issue\models\search\IssueSearch;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('issue', 'Issues Archive: {name}', [
	'name' => $searchModel->customerName ? $searchModel->customerName : $searchModel->issue_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if ($searchModel->getIssueMainType()) {
	$this->params['breadcrumbs'][] = ['label' => $searchModel->getIssueMainType()->name];
}
$this->params['issueParentTypeNav'] = [
	'route' => ['/issue/issue/index'],
];

?>
<div class="issue-archive-index">


	<?= $this->render('_grid', [
		'dataProvider' => $dataProvider,
		'searchModel' => $searchModel,
	]) ?>


</div>

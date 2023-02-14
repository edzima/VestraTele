<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\search\SummonDocLinkSearch;
use common\models\issue\SummonDocLink;
use common\modules\issue\widgets\SummonDocsLinkActionColumn;
use common\widgets\grid\CustomerDataColumn;

/* @var $this yii\web\View */
/* @var $searchModel SummonDocLinkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Summon Docs - To Confirm');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];
if ($searchModel->getIssueParentType()) {
	$this->params['breadcrumbs'][] = [
		'label' => $searchModel->getIssueParentType()->name,
		'url' => Url::issuesParentType($searchModel->getIssueParentType()->id),
	];
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Summons'), 'url' => ['/summon/index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['issueParentTypeNav'] = [
	'route' => ['/issue/summon-doc-link/to-confirm'],
];
?>

<div class="summon-doc-to-confirm">


	<?= $this->render('_nav', [
		'searchModel' => $searchModel,
	]) ?>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => IssueColumn::class,
			],
			[
				'class' => CustomerDataColumn::class,
				'attribute' => 'customerName',
			],
			[
				'attribute' => 'doc_type_id',
				'value' => 'doc.name',
				'label' => Yii::t('issue', 'Doc Name'),
				'filter' => $searchModel->getDocsNames(),
			],
			[
				'attribute' => 'summonTypeId',
				'label' => Yii::t('issue', 'Summon Type'),
				'value' => static function (SummonDocLink $docLink): string {
					return Html::a($docLink->summon->getTypeName(), [
						'summon/view', 'id' => $docLink->summon_id,
					]);
				},
				'format' => 'html',
				'filter' => $searchModel->getSummonTypesNames(),
			],
			//	'deadline_at:date',
			'done_at:datetime',
			'doneUser',
			[
				'class' => SummonDocsLinkActionColumn::class,
				'status' => $searchModel->status,
				'controller' => '/issue/summon-doc-link',
			],

		],
	]) ?>

</div>

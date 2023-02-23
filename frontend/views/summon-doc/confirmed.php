<?php

use common\models\issue\search\SummonDocLinkSearch;
use common\models\issue\SummonDocLink;
use common\modules\issue\widgets\SummonDocsLinkActionColumn;
use common\widgets\grid\CustomerDataColumn;
use frontend\helpers\Html;
use frontend\helpers\Url;
use frontend\widgets\IssueColumn;
use frontend\widgets\IssueParentTypeHeader;

/* @var $this yii\web\View */
/* @var $searchModel SummonDocLinkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Summon Docs - Confirmed');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];
if ($searchModel->getIssueParentType()) {
	$this->params['breadcrumbs'][] = [
		'label' => $searchModel->getIssueParentType()->name,
		'url' => Url::issuesParentType($searchModel->getIssueParentType()->id),
	];
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Summons'), 'url' => ['/summon/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="summon-doc-confirmed">

	<?= IssueParentTypeHeader::widget([
		'parentsMenuConfig' => [
			'route' => ['/summon-doc/confirmed'],
		],
	]) ?>

	<?= $this->render('_nav', [
		'searchModel' => $searchModel,
	]) ?>


	<?= \frontend\widgets\GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
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
			//	'deadline_at:date',
			'doneUser',
			[
				'attribute' => 'done_at',
				'format' => 'date',
				'noWrap' => true,
			],
			'confirmedUser',
			[
				'attribute' => 'confirmed_at',
				'format' => 'date',
				'noWrap' => true,
			],
			[
				'class' => SummonDocsLinkActionColumn::class,
				'status' => $searchModel->status,
				'controller' => '/summon-doc',
			],

		],
	]) ?>

</div>

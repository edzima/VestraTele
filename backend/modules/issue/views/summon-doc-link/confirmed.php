<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\search\SummonDocLinkSearch;
use common\models\issue\SummonDocLink;
use common\modules\issue\widgets\SummonDocsLinkActionColumn;
use common\widgets\grid\CustomerDataColumn;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel SummonDocLinkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Summon Docs - Confirmed');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['issue/index']];
if ($searchModel->getIssueMainType()) {
	$this->params['breadcrumbs'][] = [
		'label' => $searchModel->getIssueMainType()->name,
		'url' => Url::issuesParentType($searchModel->getIssueMainType()->id),
	];
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Summons'), 'url' => ['summon/index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['issueParentTypeNav'] = [
	'route' => ['confirmed'],
];
?>

<div class="summon-doc-confirmed">


	<?= $this->render('_nav', [
		'searchModel' => $searchModel,
	]) ?>


	<?= GridView::widget([
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
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('issue', 'Doc Name'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'multiple' => true,
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
			],

			//	'deadline_at:date',
			[
				'attribute' => 'done_at',
				'format' => 'date',
				'noWrap' => true,
			],
			[
				'attribute' => 'done_user_id',
				'value' => 'doneUser',
				'filter' => $searchModel->getDoneUsersNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => $searchModel->getAttributeLabel('done_user_id'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
			],
			[
				'attribute' => 'confirmed_at',
				'format' => 'date',
				'noWrap' => true,
			],
			[
				'attribute' => 'confirmed_user_id',
				'value' => 'confirmedUser',
				'filter' => $searchModel->getConfirmedUsersNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => $searchModel->getAttributeLabel('confirmed_user_id'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
			],
			[
				'class' => SummonDocsLinkActionColumn::class,
				'status' => $searchModel->status,
				'controller' => '/issue/summon-doc-link',
			],

		],
	]) ?>

</div>

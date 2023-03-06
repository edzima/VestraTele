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

$this->title = Yii::t('issue', 'Summon Docs - To Do');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['issue/index']];
if ($searchModel->getIssueParentType()) {
	$this->params['breadcrumbs'][] = [
		'label' => $searchModel->getIssueParentType()->name,
		'url' => Url::issuesParentType($searchModel->getIssueParentType()->id),
	];
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Summons'), 'url' => ['summon/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['issueParentTypeNav'] = [
	'route' => ['to-do'],
];
?>

<div class="summon-doc-to-do">


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
			],

		//	'deadline_at:date',
			[
				'attribute' => 'summonContractorId',
				'value' => 'summon.contractor',
				'label' => Yii::t('common', 'Contractor'),
				'filter' => $searchModel->getSummonContractorsNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('common', 'Contractor'),
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
				'attribute' => 'summonOwnerId',
				'value' => 'summon.owner',
				'label' => Yii::t('common', 'Owner'),
				'filter' => $searchModel->getSummonOwnersNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('common', 'Owner'),
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

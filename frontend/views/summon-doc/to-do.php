<?php

use common\models\issue\search\SummonDocLinkSearch;
use common\models\issue\SummonDocLink;
use common\modules\issue\widgets\SummonDocsLinkActionColumn;
use common\widgets\grid\CustomerDataColumn;
use frontend\helpers\Html;
use frontend\helpers\Url;
use frontend\widgets\GridView;
use frontend\widgets\IssueColumn;
use frontend\widgets\IssueTypeHeader;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel SummonDocLinkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $contractorsNames string[] */

$this->title = Yii::t('issue', 'Summon Docs - To Do');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];
if ($searchModel->getIssueMainType()) {
	$this->params['breadcrumbs'][] = [
		'label' => $searchModel->getIssueMainType()->name,
		'url' => Url::issuesParentType($searchModel->getIssueMainType()->id),
	];
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Summons'), 'url' => ['/summon/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="summon-doc-to-do">

	<?= IssueTypeHeader::widget([
		'navOptions' => [
			'route' => ['/summon-doc/to-do'],
		],
	]) ?>

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
				'attribute' => 'summonContractorId',
				'value' => 'summon.contractor',
				'label' => Yii::t('common', 'Contractor'),
				'filter' => $contractorsNames,
				'visible' => count($contractorsNames) > 1,
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
				'controller' => '/summon-doc',
			],

		],
	]) ?>

</div>

<?php

use backend\modules\settlement\models\search\IssueCostSearch;
use backend\modules\settlement\widgets\IssueCostActionColumn;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\helpers\Html;
use common\models\issue\IssueCost;
use common\models\issue\IssueCostInterface;
use common\widgets\grid\IssueTypeColumn;

/* @var $this yii\web\View */
/* @var $model IssueCostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Costs');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-cost-index">

	<p>
		<?= IssueCost::typeExist(IssueCost::TYPE_PCC)
			? Html::a(Yii::t('backend', 'PCC Export'), ['pcc-export', Yii::$app->request->queryParams], ['class' => 'btn btn-success'])
			: ''
		?>
		<?= IssueCost::typeExist(IssueCost::TYPE_PIT_4)
			? Html::a(Yii::t('backend', 'PIT-4 Export'), ['pit-export', Yii::$app->request->queryParams], ['class' => 'btn btn-success'])
			: ''
		?>

	</p>


	<?= $this->render('_search', ['model' => $model]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $model,
		'showPageSummary' => true,

		'columns' => [
			[
				'class' => IssueColumn::class,
			],
			[
				'class' => IssueTypeColumn::class,
				'attribute' => 'issueType',
				'label' => Yii::t('common', 'Issue type'),
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => IssueCostSearch::getTypesNames(),
			],
			[
				'attribute' => 'user_id',
				'value' => 'user',
				'label' => Yii::t('backend', 'User'),
				'filter' => IssueCostSearch::getUsersNames(),
			],

			[
				'attribute' => 'value',
				'format' => 'currency',
				'pageSummary' => true,
			],
			[
				'attribute' => 'transfer_type',
				'value' => 'transferTypeName',
				'filter' => IssueCostSearch::getTransfersTypesNames(),
			],
			[
				'attribute' => 'dateRange',
				'format' => 'date',
				'value' => 'date_at',
				'label' => $model->getAttributeLabel('date_at'),
				'enableSorting' => true,
				'filterType' => GridView::FILTER_DATE_RANGE,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'locale' => [
							'format' => 'YYYY-MM-DD',
						],
					],
				],
			],
			[
				'attribute' => 'deadlineRange',
				'format' => 'date',
				'value' => 'deadline_at',
				'label' => $model->getAttributeLabel('deadline_at'),
				'filterType' => GridView::FILTER_DATE_RANGE,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'locale' => [
							'format' => 'YYYY-MM-DD',
						],
					],
				],
			],
			[
				'attribute' => 'settledRange',
				'format' => 'date',
				'value' => 'settled_at',
				'label' => $model->getAttributeLabel('settled_at'),
				'filterType' => GridView::FILTER_DATE_RANGE,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'locale' => [
							'format' => 'YYYY-MM-DD',
						],
					],
				],
			],
			[
				'value' => static function (IssueCostInterface $cost): string {
					return count($cost->settlements);
				},
				'label' => Yii::t('settlement', 'Settlements'),
				'noWrap' => true,
			],
			//	'created_at:date',
			//	'updated_at:date',
			[
				'class' => IssueCostActionColumn::class,
			],
		],
	]) ?>

</div>

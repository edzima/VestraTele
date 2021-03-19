<?php

use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\IssueCost;
use common\models\provision\ProvisionReportSearch;
use common\models\provision\ProvisionReportSummary;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CurrencyColumn;
use common\widgets\grid\CustomerDataColumn;
use Decimal\Decimal;
use yii\data\DataProviderInterface;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $searchModel ProvisionReportSearch */
/* @var $provisionsDataProvider DataProviderInterface */
/* @var $notSettledCostsDataProvider DataProviderInterface */
/* @var $settledCostsDataProvider DataProviderInterface */
/* @var $summary ProvisionReportSummary */

$this->title = Yii::t('provision',
	'Report: {user} ({fromDate} - {toDate})', [
		'user' => $searchModel->toUser,
		'fromDate' => Yii::$app->formatter->asDate($searchModel->dateFrom),
		'toDate' => Yii::$app->formatter->asDate($searchModel->dateTo),
	]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['provision/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>

<?= $summary
	?
	DetailView::widget([
		'model' => $summary,
		'attributes' => [
			[
				'attribute' => 'provisionsSum',
				'format' => 'currency',
			],
			[
				'attribute' => 'notSettledCostsSum',
				'format' => 'currency',
			],
			[
				'attribute' => 'settledCostsSum',
				'format' => 'currency',
				'value' => $summary->getSettledCostsSum()->negate(),
			],
			[
				'attribute' => 'totalSum',
				'format' => 'currency',
			],
		],
	]) . '<br>'
	: ''
?>


<div class="row">
	<div class="col-md-6">
		<?= GridView::widget([
			'dataProvider' => $notSettledCostsDataProvider,
			'summary' => false,
			'showOnEmpty' => false,
			'emptyText' => '',
			'showPageSummary' => true,
			'caption' => Yii::t('provision', 'Not settled costs.'),
			'columns' => [
				[
					'class' => IssueColumn::class,
					'viewBaseUrl' => null,
				],
				[
					'class' => CustomerDataColumn::class,
					'value' => 'issue.customer.fullName',
				],
				[
					'class' => CurrencyColumn::class,
					'attribute' => 'valueWithoutVAT',
					'pageSummary' => true,
				],
				'date_at:date',
				[
					'class' => ActionColumn::class,
					'template' => '{view} {update}',
					'controller' => '/settlement/cost',
					'headerOptions' => [
						'class' => 'no-print',
					],
					'contentOptions' => [
						'class' => 'no-print',
					],
					'footerOptions' => [
						'class' => 'no-print',
					],
				],
			],
		]) ?>
	</div>
	<div class="col-md-6">
		<?= GridView::widget([
			'dataProvider' => $settledCostsDataProvider,
			'summary' => false,
			'showPageSummary' => true,
			'showOnEmpty' => false,
			'emptyText' => '',
			'caption' => Yii::t('provision', 'Settled costs.'),
			'columns' => [
				[
					'class' => IssueColumn::class,
					'viewBaseUrl' => null,
				],
				[
					'class' => CustomerDataColumn::class,
					'value' => 'issue.customer.fullName',
				],
				[
					'class' => CurrencyColumn::class,
					'attribute' => 'valueWithoutVAT',
					'value' => function (IssueCost $data): Decimal {
						return $data->getValueWithoutVAT()->negate();
					},
					'pageSummary' => true,
				],
				'date_at:date',
				'settled_at:date',
				[
					'class' => ActionColumn::class,
					'template' => '{view} {update}',
					'controller' => '/settlement/cost',
					'headerOptions' => ['class' => 'no-print',],
					'contentOptions' => ['class' => 'no-print',],
					'footerOptions' => ['class' => 'no-print',],
				],
			],
		]) ?>
	</div>

</div>


<?= GridView::widget([
	'id' => 'report-grid',
	'dataProvider' => $provisionsDataProvider,
	'summary' => false,
	'caption' => Yii::t('provision', 'Provisions'),
	'showPageSummary' => true,
	'columns' => [
		[
			'class' => IssueColumn::class,
			'viewBaseUrl' => null,
		],
		[
			'class' => CustomerDataColumn::class,
			'value' => 'pay.issue.customer.fullName',
		],
		'fromUserString',
		[
			'label' => 'Płatność',
			'value' => 'pay.partInfo',
		],
		[
			'label' => 'Typ',
			'value' => 'type.name',
		],
		[
			'attribute' => 'pay.valueWithoutVAT',
			'label' => Yii::t('settlement', 'Pay Value without VAT'),
			'format' => 'currency',
		],
		'provision:percent',
		[
			'class' => CurrencyColumn::class,
			'pageSummary' => true,
		],
		[
			'class' => ActionColumn::class,
			'template' => '{delete}',
			'options' => ['class' => 'no-print',],
			'headerOptions' => ['class' => 'no-print',],
			'contentOptions' => ['class' => 'no-print',],
			'footerOptions' => ['class' => 'no-print',],
		],
	],
])
?>



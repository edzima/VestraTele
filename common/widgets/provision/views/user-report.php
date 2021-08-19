<?php

use backend\modules\settlement\widgets\IssueCostActionColumn;
use common\models\provision\ProvisionReportSummary;
use common\models\settlement\VATInfo;
use common\widgets\grid\CurrencyColumn;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\GridView;
use Decimal\Decimal;
use yii\widgets\DetailView;

/* @var $this \yii\web\View */
/* @var $model ProvisionReportSummary */
/* @var $issueColumn array */
/* @var $actionColumn array */

?>

<div class="provision user-report">
	<?= $model->settledCostsDataProvider->getTotalCount() > 0 || $model->notSettledCostsDataProvider->getTotalCount() > 0
		? DetailView::widget([
			'model' => $model,
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
					'value' => $model->getSettledCostsSum()->negate(),
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
				'dataProvider' => $model->notSettledCostsDataProvider,
				'summary' => false,
				'showOnEmpty' => false,
				'emptyText' => '',
				'showPageSummary' => true,
				'caption' => Yii::t('provision', 'Not settled costs'),
				'columns' => [
					$issueColumn,
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
						'class' => IssueCostActionColumn::class,
					],
				],
			]) ?>
		</div>
		<div class="col-md-6">
			<?= GridView::widget([
				'dataProvider' => $model->settledCostsDataProvider,
				'summary' => false,
				'showPageSummary' => true,
				'showOnEmpty' => false,
				'emptyText' => '',
				'caption' => Yii::t('provision', 'Settled costs'),
				'columns' => [
					$issueColumn,
					[
						'class' => CustomerDataColumn::class,
						'value' => 'issue.customer.fullName',
					],
					[
						'class' => CurrencyColumn::class,
						'attribute' => 'valueWithoutVAT',
						'value' => static function (VATInfo $data): Decimal {
							return $data->getValueWithoutVAT()->negate();
						},
						'pageSummary' => true,
					],
					'date_at:date',
					'settled_at:date',
					[
						'class' => IssueCostActionColumn::class,
					],
				],
			]) ?>
		</div>

	</div>


	<?= GridView::widget([
		'id' => 'report-grid',
		'dataProvider' => $model->provisionsDataProvider,
		'caption' => Yii::t('provision', 'Provisions'),
		'showPageSummary' => true,
		'columns' => [
			$issueColumn,
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
			'provision',
			[
				'class' => CurrencyColumn::class,
				'pageSummary' => true,
			],
			$actionColumn,
		],
	])
	?>


</div>

<?php if ($model->provisionsDataProvider->pagination->pageCount > 1): ?>
	<p><?= Yii::t('provision', 'Sum {value}', [
			'value' => Yii::$app->formatter->asCurrency($model->getProvisionsTotalSum()),
		]) ?></p>
<?php endif; ?>

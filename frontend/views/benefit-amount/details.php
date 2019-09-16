<?php

use common\models\benefit\BenefitAmountDivider;
use frontend\models\BenefitAmountAlignmentForm;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use kartik\grid\SerialColumn;
use yii\data\ArrayDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $model BenefitAmountAlignmentForm */
/* @var $dataProvider ArrayDataProvider */

?>



<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'summary' => false,
	'showPageSummary' => true,
	'responsive' => true,
	'hover' => true,
	'pageSummaryPosition' => GridView::POS_TOP,
	'headerRowOptions' => ['class' => 'kartik-sheet-style'],
	'columns' => [
		['class' => SerialColumn::class],
		[
			'attribute' => 'month',
			'value' => static function (BenefitAmountDivider $model): string {
				return date('m-Y', $model->getMonth());
			},
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'smaller',
			'pageSummary' => true,
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'greater',
			'pageSummary' => true,
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'diff',
			'pageSummary' => true,
		],
	],
]); ?>





<?php

use backend\modules\settlement\models\search\PayReceivedSearch;
use backend\widgets\GridView;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\AgentDataColumn;
use common\widgets\grid\CurrencyColumn;
use common\widgets\grid\CustomerDataColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel PayReceivedSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('settlement', 'Received pays');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pay-received-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'showPageSummary' => true,
		'columns' => [
			[
				'attribute' => 'calculationType',
				'value' => 'pay.calculation.typeName',
				'filter' => PayReceivedSearch::getCalculationTypesNames(),
			],
			[
				'attribute' => 'user_id',
				'value' => 'user',
				'filter' => PayReceivedSearch::getUserNames(),
			],
			[
				'attribute' => 'issueAgent',

				'class' => AgentDataColumn::class,
				'value' => 'pay.calculation.issue.agent',

			],
			[
				'class' => CustomerDataColumn::class,
				'value' => 'pay.calculation.issue.customer',
			],
			[
				'class' => CurrencyColumn::class,
				'attribute' => 'pay.value',
				'pageSummary' => true,
			],
			'date_at:date',
			'transfer_at:date',
			[
				'class' => ActionColumn::class,
			],
		],
	]); ?>


</div>

<?php

use common\widgets\grid\CurrencyColumn;
use common\widgets\grid\CustomerDataColumn;
use frontend\models\search\PayReceivedSearch;
use frontend\widgets\GridView;
use frontend\widgets\IssueColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel PayReceivedSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('settlement', 'Received pays');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['settlement/index']];
$this->params['breadcrumbs'][] =['label' => Yii::t('settlement', 'Pays'), 'url' => ['pay/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pay-received-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="row">
		<div class="col-md-5">
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'showPageSummary' => true,
				'columns' => [
					[
						'class' => IssueColumn::class,
						'attribute' => 'pay.calculation.issue_id',
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
					[
						'attribute' => 'date_at',
						'format' => 'date',
						'width' => '100px',
					],

				],
			]) ?>
		</div>
	</div>


</div>

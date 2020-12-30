<?php

use backend\modules\settlement\models\search\PayReceivedSearch;
use backend\widgets\GridView;
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


	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'calculationType',
				'value' => 'pay.calculation.typeName',
				'filter' => PayReceivedSearch::getCalculationTypesNames(),
			],
			[
				'attribute' => 'user_id',
				'value' => 'user',
				'label' => 'Odbiorca',
				'filter' => PayReceivedSearch::getUserNames(),
			],
			'date_at:date',
			'transfer_at:date',
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>

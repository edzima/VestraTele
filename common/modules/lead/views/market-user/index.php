<?php

use common\helpers\Html;
use common\modules\lead\models\searches\LeadMarketUserSearch;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel LeadMarketUserSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Market Users');

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Markets'), 'url' => ['market/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-market-user-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'market_id',
			[
				'attribute' => 'status',
				'filter' => LeadMarketUserSearch::getStatusesNames(),
				'value' => 'statusName',
			],
			'days_reservation',
			'details:ntext',
			'created_at:datetime',
			//'updated_at',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>

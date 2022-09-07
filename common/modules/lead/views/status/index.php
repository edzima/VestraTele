<?php

use common\modules\lead\models\searches\LeadStatusSearch;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel LeadStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Statuses');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-status-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead Status'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'id',
			'name',
			'description',
			'short_report:boolean',
			'show_report_in_lead_index:boolean',
			'not_for_dialer:boolean',
			'sort_index',
			[
				'attribute' => 'market_status',
				'value' => 'marketStatusName',
				'filter' => LeadStatusSearch::getMarketStatusesNames(),
			],

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>

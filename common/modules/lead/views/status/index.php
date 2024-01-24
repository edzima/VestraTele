<?php

use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\searches\LeadStatusSearch;
use common\widgets\GridView;
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
			[
				'header' => '#',
				'attribute' => 'id',
				'width' => '65px',
				'contentCenter' => true,
			],
			'name',
			//	'description',
			'days_deadline',
			'short_report:boolean',
			'show_report_in_lead_index:boolean',
			//		'not_for_dialer:boolean',
			[
				'attribute' => 'calendar_background',
				'contentOptions' => static function (LeadStatus $data): array {
					$options = [];
					if (!empty($data->calendar_background)) {
						$options['style']['background-color'] = $data->calendar_background;
					}
					return $options;
				},
			],
			[
				'attribute' => 'statuses',
				'format' => 'html',
				'value' => function (LeadStatus $data): string {
					return Html::ul($data->getStatusesNames());
				},
			],
			[
				'attribute' => 'market_status',
				'value' => 'marketStatusName',
				'filter' => LeadStatusSearch::getMarketStatusesNames(),
			],
			[
				'header' => '↑ ↓',
				'attribute' => 'sort_index',
				'width' => '65px',
				'contentCenter' => true,
			],
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>

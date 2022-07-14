<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\searches\LeadMarketSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel LeadMarketSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Markets');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-market-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<p>
		<?= Html::a(Yii::t('lead', 'Market Users'), ['market-user/index',], [
			'class' => 'btn btn-success',
		]) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			//'id',
			'lead.name',
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => LeadMarketSearch::getStatusesNames(),
			],
			'details:ntext',

			[
				'attribute' => 'visibleArea',
				'value' => 'marketOptions.visibleAreaName',
				'label' => Yii::t('lead', 'Visible Area'),
				'filter' => LeadMarketSearch::getVisibleAreaNames(),
			],
			'creator.fullName',
			[
				'attribute' => 'usersCount',
			],
			'created_at:datetime',

			[
				'class' => ActionColumn::class,
				'template' => '{access-request} {view} {update} {delete}',
				'buttons' => [
					'access-request' => function (string $url, LeadMarket $data): ?string {
						if ($data->isDone() || $data->isArchived()) {
							return null;
						}

						return Html::a('<i class="fa fa-unlock" aria-hidden="true"></i>', ['market-user/access-request', 'market_id' => $data->id]);
					},
				],
			],
		],
	]); ?>


</div>

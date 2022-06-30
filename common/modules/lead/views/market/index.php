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
			'lead_id',
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => LeadMarketSearch::getStatusesNames(),
			],
			'details:ntext',

			'created_at:datetime',
			[
				'attribute' => 'booleanOptions',
				'value' => function (LeadMarket $data): string {
					$attributes = $data->getMarketOptions()->booleanTrueAttributeLabels();
					if (empty($attributes)) {
						return '';
					}
					return implode(', ', $attributes);
				},
			],
			//'updated_at',
			//'options:ntext',

			[
				'class' => ActionColumn::class,
				'template' => '{access-request} {view} {update} {delete}',
				'buttons' => [
					'access-request' => function (string $url, LeadMarket $data): ?string {
						if ($data->isDone() || $data->isArchived()) {
							return null;
						}

						return Html::a(Html::icon('check'), ['market-user/access-request', 'market_id' => $data->id]);
					},
				],
			],
		],
	]); ?>


</div>

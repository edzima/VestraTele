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
<div class="lead-market-user">

	<h1><?= Html::encode($this->title) ?></h1>


	<p>
		<?= Html::a(Yii::t('lead', 'Market Users'), ['market-user/index',], [
			'class' => 'btn btn-success',
		]) ?>
	</p>

	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			//'id',
			[
				'attribute' => 'addressDetails',
			],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => LeadMarketSearch::getStatusesNames(),
			],
			'details:ntext',
			'created_at:datetime',
			[
				'attribute' => 'creator_id',
				'value' => 'creator.fullName',
				'label' => Yii::t('lead', 'Creator'),
				'visible' => !$searchModel->selfMarket,
				'filter' => $searchModel::getCreatorsNames(),
			],
			//'updated_at',
			//'options:ntext',
			[
				'attribute' => 'usersCount',
			],
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
				'visibleButtons' => [
					'update' => function (LeadMarket $data): bool {
						return $data->isCreatorOrOwnerLead(Yii::$app->user->getId());
					},
					'delete' => function (LeadMarket $data): bool {
						return $data->status === LeadMarket::STATUS_NEW && $data->isCreatorOrOwnerLead(Yii::$app->user->getId());
					},
				],
			],
		],
	]); ?>


</div>
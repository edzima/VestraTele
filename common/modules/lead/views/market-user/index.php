<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarketUser;
use common\modules\lead\models\searches\LeadMarketUserSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel LeadMarketUserSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('lead', 'Market Users');

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
			'created_at:date',
			'reserved_at:date',
			//'updated_at',
			[
				'class' => ActionColumn::class,
				'template' => '{view} {access-request} {accept} {reject} {delete}',
				'visibleButtons' => [
					'access-request' => static function (LeadMarketUser $model) {
						return $model->user_id === Yii::$app->user->getId();
					},
					'accept' => static function (LeadMarketUser $model): bool {
						return $model->isToConfirm() && $model->market->isCreatorOrOwnerLead(Yii::$app->user->getId());
					},
					'delete' => static function (LeadMarketUser $model) {
						return $model->isToConfirm() && $model->user_id === Yii::$app->user->getId();
					},
					'reject' => static function (LeadMarketUser $model): bool {
						return $model->isToConfirm() && $model->market->isCreatorOrOwnerLead(Yii::$app->user->getId());
					},
				],
				'buttons' => [
					'view' => static function (string $url, LeadMarketUser $model): string {
						return Html::a(Html::icon('eye-open'), ['market/view', 'id' => $model->market_id]);
					},
					'accept' => static function (string $url): string {
						return Html::a(Html::icon('check'), $url);
					},
					'access-request' => static function (string $url): string {
						return Html::a('<i class="fa fa-unlock" aria-hidden="true"></i>', $url);
					},
					'reject' => static function (string $url): string {
						return Html::a(Html::icon('remove'), $url);
					},
				],
			],
		],
	]); ?>


</div>

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
if ($searchModel->scenario === LeadMarketUserSearch::SCENARIO_USER_MARKET) {
	$this->title = Yii::t('lead', 'Access Request to self Market');
}
if ($searchModel->scenario === LeadMarketUserSearch::SCENARIO_USER) {
	$this->title = Yii::t('lead', 'Self Access Request');
}

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Markets'), 'url' => ['market/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-market-user-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'value' => 'market.lead.name',
				'label' => Yii::t('lead', 'Lead Name'),
			],
			[
				'attribute' => 'marketStatus',
				'filter' => LeadMarketUserSearch::getMarketStatusesNames(),
				'value' => 'market.statusName',
				'label' => $searchModel->getAttributeLabel('marketStatus'),
			],
			[
				'attribute' => 'marketCreatorId',
				'filter' => LeadMarketUserSearch::getMarketCreatorsNames(),
				'value' => 'market.creator.fullName',
				'label' => Yii::t('lead', 'Creator'),
				'visible' => $searchModel->scenario !== LeadMarketUserSearch::SCENARIO_USER_MARKET,
			],
			[
				'attribute' => 'status',
				'filter' => LeadMarketUserSearch::getStatusesNames(),
				'value' => 'statusName',
			],
			[
				'attribute' => 'user_id',
				'filter' => LeadMarketUserSearch::getUsersNames(),
				'value' => 'user.fullName',
				'label' => Yii::t('lead', 'User'),
				'visible' => $searchModel->scenario !== LeadMarketUserSearch::SCENARIO_USER,
			],
			'days_reservation',
			'details:ntext',
			'created_at:date',
			'reserved_at:date',
			[
				'class' => ActionColumn::class,
				'template' => '{accept} {give-up} {reject} {view} {delete}',
				'visibleButtons' => [
					'accept' => static function (LeadMarketUser $model): bool {
						return $model->isToConfirm() && $model->market->isCreatorOrOwnerLead(Yii::$app->user->getId());
					},
					'delete' => static function (LeadMarketUser $model) {
						return $model->isToConfirm() && $model->user_id === Yii::$app->user->getId();
					},
					'give-up' => static function (LeadMarketUser $data): bool {
						return $data->isAllowGiven() && Yii::$app->user->getId() === $data->user_id;
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
						return Html::a(Html::icon('ok'), $url, [
							'title' => Yii::t('lead', 'Accept'),
							'aria-label' => Yii::t('lead', 'Accept'),
							'data-pjax' => 1,
							'data-method' => 'POST',
						]);
					},
					'give-up' => static function (string $url): string {
						return Html::a(Html::icon('remove'), $url, [
							'title' => Yii::t('lead', 'Give Up'),
							'aria-label' => Yii::t('lead', 'Give Up'),
							'data-pjax' => 1,
							'data-method' => 'POST',
						]);
					},
					'reject' => static function (string $url): string {
						return Html::a(Html::icon('minus'), $url, [
							'title' => Yii::t('lead', 'Reject'),
							'aria-label' => Yii::t('lead', 'Reject'),
							'data-pjax' => 1,
							'data-method' => 'POST',
						]);
					},
				],
			],
		],
	]);

	?>


</div>

<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadMarket */
/* @var $onlyUser bool */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->lead->getName(), 'url' => ['lead/view', 'id' => $model->lead_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Markets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);
?>
<div class="lead-market-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= !$model->userCanAccessRequest(Yii::$app->user->getId())
			? Html::a(Yii::t('lead', 'Request Access'), ['market-user/access-request', 'market_id' => $model->id], ['class' => 'btn btn-success'])
			: ''
		?>
		<?= $model->isCreatorOrOwnerLead(Yii::$app->user->getId()) || !$onlyUser
			? Html::a(Yii::t('lead', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
			: ''
		?>
		<?= $model->isCreatorOrOwnerLead(Yii::$app->user->getId()) || !$onlyUser
			? Html::a(Yii::t('lead', 'Delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger',
				'data' => [
					'confirm' => Yii::t('lead', 'Are you sure you want to delete this item?'),
					'method' => 'post',
				],
			])
			: '' ?>
	</p>

	<div class="row">


		<div class="col-md-5">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'statusName',
					'created_at:datetime',
					'updated_at:datetime',
					'creator.fullName:text:' . Yii::t('lead', 'Creator'),
					'lead.owner.fullName:text:' . Yii::t('lead', 'Owner'),
				],
			]) ?>
		</div>


		<div class="col-md-4">
			<?= $model->isCreatorOrOwnerLead(Yii::$app->user->getId())
				? DetailView::widget([
					'model' => $model->getMarketOptions(),
					'attributes' => [
						'visibleAreaName',
						'visibleAddressDetails:boolean',
					],
				])
				: ''
			?>
		</div>
	</div>

	<?= GridView::widget([
		'dataProvider' => new ActiveDataProvider([
			'query' => $model->getLeadMarketUsers()
				->orderBy(['created_at' => SORT_ASC]),
		]),
		'summary' => false,
		'columns' => [
			'user.fullName',
			'statusName',
			'days_reservation',
			'details',
			'created_at:datetime',
			//	'updated_at:datetime',
			'reserved_at:date',
			[
				'class' => ActionColumn::class,
				'controller' => 'market-user',
				'template' => '{accept} {reject} {delete}',
				'visibleButtons' => [
					'accept' => static function (LeadMarketUser $data) use ($model): bool {
						return $data->isToConfirm() && $model->isCreatorOrOwnerLead(Yii::$app->user->getId());
					},
					'delete' => static function (LeadMarketUser $model) {
						return $model->isToConfirm() && $model->user_id === Yii::$app->user->getId();
					},
					'reject' => static function (LeadMarketUser $data) use ($model): bool {
						return $data->isToConfirm() && $model->isCreatorOrOwnerLead(Yii::$app->user->getId());
					},
				],
				'buttons' => [
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
	]) ?>


</div>

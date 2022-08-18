<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\widgets\address\AddressDetailView;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadMarket */
/* @var $onlyUser bool */

$showLead = !$onlyUser || $model->hasAccessToLead(Yii::$app->user->getId());

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
if ($showLead) {
	$this->params['breadcrumbs'][] = ['label' => $model->lead->getName(), 'url' => ['lead/view', 'id' => $model->lead_id]];
} else {
	$this->params['breadcrumbs'][] = $model->lead->getName();
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Markets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);
?>
<div class="lead-market-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= $model->userCanAccessRequest(Yii::$app->user->getId())
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
					[
						'value' => $model->getUser(Yii::$app->user->getId())
							? $model->getUser(Yii::$app->user->getId())->reserved_at
							: null,
						'format' => 'date',
						'label' => Yii::t('lead', 'Reserved At'),
						'visible' => $model->getUser(Yii::$app->user->getId()) !== null,
					],
				],
			]) ?>


			<?= $showLead
				? $this->render('_lead', [
					'model' => $model->lead,
				])
				: ''
			?>


			<?= $showLead && $model->lead->getCustomerAddress()
				? AddressDetailView::widget([
					'model' => $model->lead->getCustomerAddress(),
				])
				: ''
			?>
		</div>


		<div class="col-md-4">
			<?= $model->isCreatorOrOwnerLead(Yii::$app->user->getId())
				? $this->render('_options_details', [
					'model' => $model->getMarketOptions(),
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
				'template' => '{accept} {give-up} {reject} {delete}',
				'visibleButtons' => [
					'accept' => static function (LeadMarketUser $data) use ($model): bool {
						return $data->isToConfirm() && $model->isCreatorOrOwnerLead(Yii::$app->user->getId());
					},
					'delete' => static function (LeadMarketUser $data) {
						return $data->isToConfirm() && $data->user_id === Yii::$app->user->getId();
					},
					'give-up' => static function (LeadMarketUser $data): bool {
						return $data->isAllowGiven() && Yii::$app->user->getId() === $data->user_id;
					},
					'reject' => static function (LeadMarketUser $data) use ($model): bool {
						return $data->isToConfirm() && $model->isCreatorOrOwnerLead(Yii::$app->user->getId());
					},

				],
				'buttons' => [
					'access-request' => static function (string $url): string {
						return Html::a('<i class="fa fa-unlock" aria-hidden="true"></i>', $url, [
							'title' => Yii::t('lead', 'Request Access'),
							'aria-label' => Yii::t('lead', 'Request Access'),
							'data-pjax' => 0,
						]);
					},
					'accept' => static function (string $url): string {
						return Html::a(Html::icon('ok'), $url, [
							'title' => Yii::t('lead', 'Accept'),
							'aria-label' => Yii::t('lead', 'Accept'),
							'data-pjax' => 1,
						]);
					},
					'give-up' => static function (string $url): string {
						return Html::a(Html::icon('remove'), $url, [
							'title' => Yii::t('lead', 'Give Up'),
							'aria-label' => Yii::t('lead', 'Give Up'),
							'data-pjax' => 1,
						]);
					},
					'reject' => static function (string $url): string {
						return Html::a(Html::icon('minus'), $url, [
							'title' => Yii::t('lead', 'Reject'),
							'aria-label' => Yii::t('lead', 'Reject'),
							'data-pjax' => 1,
						]);
					},
				],
			],
		],
	]) ?>


</div>

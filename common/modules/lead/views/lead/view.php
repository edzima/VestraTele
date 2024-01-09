<?php

use common\helpers\Html;
use common\helpers\Url;
use common\models\user\User;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\models\LeadUser;
use common\modules\lead\widgets\CopyLeadBtnWidget;
use common\modules\lead\widgets\LeadAnswersWidget;
use common\modules\lead\widgets\LeadReportWidget;
use common\modules\lead\widgets\LeadSmsBtnWidget;
use common\modules\lead\widgets\SameContactsListWidget;
use common\modules\lead\widgets\ShortReportStatusesWidget;
use common\widgets\address\AddressDetailView;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\DataProviderInterface;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ActiveLead */
/* @var $sameContacts LeadInterface[] */
/* @var $withDelete bool */
/* @var $onlyUser bool */
/* @var $isOwner bool */
/* @var $userIsFromMarket bool */
/* @var $remindersDataProvider DataProviderInterface */
/* @var $usersDataProvider null|DataProviderInterface */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

?>
<div class="lead-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p class="d-inline">

		<?= Html::a(Yii::t('lead', 'Report'), ['report/report', 'id' => $model->getId(), 'hash' => $model->getHash()], ['class' => 'btn btn-success']) ?>

		<?= ShortReportStatusesWidget::widget(['lead_id' => $model->getId()]) ?>

		<?= !$userIsFromMarket
			? Html::a(Yii::t('lead', 'Update'), ['update', 'id' => $model->getId()], ['class' => 'btn btn-primary'])
			: ''
		?>


		<?= (!$onlyUser || $isOwner)
		&& Yii::$app->user->can(User::PERMISSION_LEAD_MARKET)
		&& $model->market === null
			? Html::a(Yii::t('lead', '-> Market'),
				['market/create', 'id' => $model->getId()],
				['class' => 'btn btn-success'])
			: ''
		?>


		<?= !$userIsFromMarket && $model->getStatusId() !== LeadStatusInterface::STATUS_ARCHIVE
			? Html::a(Yii::t('lead', 'Archive'), ['archive/self', 'id' => $model->getId()], [
				'class' => 'btn btn-danger',
				'data' => [
					'method' => 'POST',
					'confirm' => Yii::t('lead', 'Move Lead: {lead} to Archive?', [
						'lead' => $model->getName(),
					]),
				],
			])
			: ''
		?>


		<span class="pull-right">


	<div class="pull-right d-inline">

		<?= !$userIsFromMarket
			? CopyLeadBtnWidget::widget([
				'leadId' => $model->getId(),
			])
			: ''
		?>


		<?= Yii::$app->user->can(User::PERMISSION_LEAD_SMS_WELCOME)
			? LeadSmsBtnWidget::widget([
				'model' => $model,
			])
			: ''
		?>


		<?= !$userIsFromMarket ?
			Html::a(Yii::t('lead', 'Assign User'), ['user/assign-single', 'id' => $model->getId()],
				['class' => 'btn btn-info'])
			: ''
		?>

		<?= $withDelete
			? Html::a(Yii::t('lead', 'Delete'), ['delete', 'id' => $model->getId()], [
				'class' => 'btn btn-danger',
				'data' => [
					'confirm' => Yii::t('lead', 'Are you sure you want to delete this item?'),
					'method' => 'post',
				],
			])
			: ''
		?>
	</div>


	<p></p>


	<?php if (!empty($model->reports)): ?>
		<?php foreach ($model->reports as $report): ?>

			<?= $report->is_pinned
				? LeadReportWidget::widget([
					'model' => $report,
					'withDeleteButton' => false,
				])
				: '' ?>


		<?php endforeach; ?>
	<?php endif; ?>

	<div class="row">
		<div class="col-md-4">


			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'status',
					[
						'attribute' => 'source.type.nameWithDescription',
						'label' => Yii::t('lead', 'Type'),
					],
					'source',
					[
						'attribute' => 'campaign',
						'visible' => !empty($model->campaign_id),
					],
					'date_at:datetime',
					[
						'attribute' => 'updated_at',
						'format' => 'datetime',
						'visible' => !empty($model->updated_at),
					],
					[
						'attribute' => 'details',
						'visible' => !empty($model->getDetails()),
						'format' => 'ntext',
					],
					[
						'attribute' => 'data',
						'visible' => !empty($model->getData())
							&& Yii::$app->user->can(User::ROLE_ADMINISTRATOR),
						'format' => 'ntext',
					],
					[
						'attribute' => 'customerUrl',
						'format' => 'html',
						'label' => Yii::t('lead', 'Customer View'),
						'visible' => !$onlyUser && isset($model->getData()['customerUrl']),
						'value' => isset($model->getData()['customerUrl'])
							? Html::a($model->getName(), $model->getData()['customerUrl'])
							: '',
					],
					[
						'attribute' => 'phone',
						'format' => 'tel',
						'visible' => !empty($model->getPhone()),
					],
					[
						'attribute' => 'email',
						'format' => 'email',
						'visible' => !empty($model->getEmail()),
					],
					[
						'attribute' => 'postal_code',
						'visible' => !empty($model->postal_code),
					],
					[
						'attribute' => 'providerName',
						'visible' => !empty($model->provider),
					],
				],
			]) ?>

			<?php
			//			LeadDialersGridView::widget([
			//				'lead' => $model,
			//			])
			// ?>

		</div>
		<div class="col-md-8">

			<div class="row">

				<div class="col-md-12">


					<?= $this->render('_reminder-grid', [
						'model' => $model,
						'onlyUser' => $onlyUser,
						'dataProvider' => $remindersDataProvider,
					]) ?>

				</div>
				<div class="clearfix"></div>

				<div class="col-md-12">
					<?= LeadAnswersWidget::widget(['answers' => $model->answers]) ?>

				</div>

				<?= $model->getCustomerAddress()
					? Html::tag('div',
						AddressDetailView::widget(['model' => $model->getCustomerAddress(),]), [
							'class' => 'col-md-3',
						])
					: ''
				?>

				<?= $usersDataProvider !== null
					? GridView::widget([
						'options' => ['class' => 'col-md-4',],
						'caption' => Yii::t('lead', 'Users'),
						'dataProvider' => $usersDataProvider,
						'showOnEmpty' => false,
						'emptyText' => false,
						'summary' => false,
						'columns' => [
							[
								'label' => Yii::t('lead', 'User'),
								'value' => 'userWithTypeName',
							],
							[
								'label' => Yii::t('lead', 'Date At'),
								'value' => 'formattedDates',
							],
							[
								'class' => ActionColumn::class,
								'template' => '{update} {delete}',
								'urlCreator' => function (string $action, LeadUser $user): string {
									return Url::to([
										'/lead/user/' . $action,
										'lead_id' => $user->lead_id,
										'user_id' => $user->user_id,
										'type' => $user->type,
										'returnUrl' => Url::current(),
									]);
								},
							],
						],
					])
					: '' ?>

			</div>

			<div class="clearfix"></div>


			<?= SameContactsListWidget::widget([
				'model' => $model,
				'viewLink' => !$onlyUser,
				'visibleCustomerLink' => !$onlyUser,
				'updateLink' => !$onlyUser && Yii::$app->user->can(User::PERMISSION_LEAD_DUPLICATE),
				'headerOptions' => ['class' => 'col-md-12',],
				'archiveBtn' => Yii::$app->user->can(User::PERMISSION_LEAD_DUPLICATE) && !$userIsFromMarket,
				'withType' => false,
				'withDialers' => true,
				'options' => ['class' => 'row',],
				'itemOptions' => ['class' => 'col-md-6',],
			]) ?>
			<div class="clearfix"></div>

		</div>
	</div>
	<div class="clearfix"></div>

	<?php if (!empty($model->reports)): ?>
		<h4><?= Yii::t('lead', 'Reports') ?></h4>
		<?php foreach ($model->reports as $report): ?>

			<?= LeadReportWidget::widget([
				'model' => $report,
				'withDeleteButton' => false,
			]) ?>


		<?php endforeach; ?>
	<?php endif; ?>

</div>

<?php

use common\helpers\Url;
use common\modules\lead\models\Lead;
use common\modules\lead\Module;
use common\modules\lead\widgets\LeadAnswersWidget;
use common\modules\lead\widgets\LeadReportWidget;
use common\modules\reminder\widgets\ReminderGridWidget;
use common\widgets\address\AddressDetailView;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Lead */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

?>
<div class="lead-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>

		<?= Html::a(Yii::t('lead', 'Report'), ['report/report', 'id' => $model->id], ['class' => 'btn btn-success']) ?>

		<?= Html::a(Yii::t('lead', 'Create Reminder'), ['reminder/create', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>

		<?= Html::a(Yii::t('lead', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

		<?= Module::getInstance()->allowDelete
			? Html::a(Yii::t('lead', 'Delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger',
				'data' => [
					'confirm' => Yii::t('lead', 'Are you sure you want to delete this item?'),
					'method' => 'post',
				],
			]) : '' ?>
	</p>

	<div class="row">
		<div class="col-md-6">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'status',
					[
						'attribute' => 'source.type',
						'label' => Yii::t('lead', 'Type'),
					],
					'source',
					[
						'attribute' => 'campaign',
						'visible' => !empty($model->campaign_id),
					],
					'date_at:datetime',
					[
						'attribute' => 'data',
						'visible' => !empty($model->getData()),
						'format' => 'ntext',
					],
					'phone',
					'email:email',
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


		</div>
		<div class="col-md-6">


			<?= LeadAnswersWidget::widget([
				'answers' => $model->answers,
			]) ?>


			<?= $model->getCustomerAddress() ? AddressDetailView::widget([
				'model' => $model->getCustomerAddress(),
			]) : '' ?>

			<?= !Module::getInstance()->onlyUser || count($model->getUsers()) > 1
				? GridView::widget([
					'caption' => Yii::t('lead', 'Users'),
					'dataProvider' => new ActiveDataProvider(['query' => $model->getLeadUsers()->with('user.userProfile')]),
					'showOnEmpty' => false,
					'emptyText' => false,
					'summary' => false,
					'columns' => [
						'typeName',
						[
							'label' => Yii::t('lead', 'User'),
							'value' => 'user.fullName',
						],
					],
				])
				: '' ?>

			<?= ReminderGridWidget::widget([
				'dataProvider' => new ActiveDataProvider(['query' => $model->getReminders()]),
				'urlCreator' => function ($action, $reminder, $key, $index) use ($model) {
					return Url::toRoute([
						'reminder/' . $action,
						'reminder_id' => $reminder->id,
						'lead_id' => $model->getId(),
					]);
				},
			]) ?>

		</div>
	</div>

	<?php foreach ($model->reports as $report): ?>

		<?= LeadReportWidget::widget([
			'model' => $report,
		]) ?>


	<?php endforeach; ?>

</div>

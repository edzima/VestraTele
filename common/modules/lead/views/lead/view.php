<?php

use common\modules\lead\models\Lead;
use common\modules\lead\widgets\LeadAnswersWidget;
use common\modules\lead\widgets\LeadReportWidget;
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

		<?= Html::a(Yii::t('lead', 'Create Reminder'), ['remininder/create', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>

		<?= Html::a(Yii::t('lead', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

		<?= Html::a(Yii::t('lead', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('lead', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<div class="row">
		<div class="col-md-6">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'status',
					'source.type',
					'source',
					'campaign',
					'date_at',
					'data:ntext',
					'phone',
					'email:email',
					'postal_code',
					'providerName',
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

			<?= GridView::widget([
				'dataProvider' => new ActiveDataProvider(['query' => $model->getLeadUsers()->with('user.userProfile')]),
				'showOnEmpty' => false,
				'emptyText' => false,
				'summary' => false,
				'columns' => [
					'type', [
						'label' => Yii::t('lead', 'User'),
						'value' => 'user.fullName',
					],
				],
			]) ?>

		</div>
	</div>

	<?php foreach ($model->reports as $report): ?>

		<?= LeadReportWidget::widget([
			'model' => $report,
		]) ?>


	<?php endforeach; ?>

</div>

<?php

use common\modules\lead\models\Lead;
use common\modules\lead\widgets\LeadAnswersWidget;
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


	<div class="row">


		<?php foreach ($model->reports as $report): ?>
			<div class="col-md-6">

				<h4><?= $report->getDateTitle() ?></h4>

				<?php if ($report->isChangeStatus()): ?>
					<p><?= Yii::t('lead', 'Change status from: {oldStatus} to: {status}', [
							'oldStatus' => $report->oldStatus->name,
							'status' => $report->status->name,
						]) ?>
					</p>
				<?php endif; ?>


				<?= DetailView::widget([
					'model' => $report,
					'attributes' => [
						[
							'attribute' => 'details',
							'visible' => !empty($report->details),
						],
						//		'owner',
						[
							'label' => Yii::t('lead', 'Answer count'),
							'value' => count($report->answers),
							'visible' => !empty($report->answers),
						],
						[
							'attribute' => 'answersQuestions',
							'visible' => !empty($report->answers),
						],
					],
				]) ?>

			</div>

		<?php endforeach; ?>
	</div>

</div>

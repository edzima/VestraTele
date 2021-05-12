<?php

use common\modules\lead\models\Lead;
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

		<?= Html::a(Yii::t('lead', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('lead', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('lead', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

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

	<?= GridView::widget([
		'dataProvider' => new ActiveDataProvider(['query' => $model->getLeadUsers()->with('user.userProfile')]),
		'showOnEmpty' => false,
		'emptyText' => false,
		'columns' => [
			'type', [
				'label' => Yii::t('lead', 'User'),
				'value' => 'user.fullName',
			],
		],
	]) ?>



	<?php foreach ($model->reports as $report): ?>
		<h4><?= $report->getDateTitle() ?></h4>
		<?= DetailView::widget([
			'model' => $report,
			'attributes' => [
				'owner',
				'details',
				[
					'attribute' => 'oldStatus',
					'visible' => $report->old_status_id !== $report->status_id,
				],
				[
					'attribute' => 'status',
					'visible' => $report->old_status_id !== $report->status_id,
				],
			],
		]) ?>

		<?php foreach ($report->answers as $answer): ?>
			<?= DetailView::widget([
				'model' => $answer,
				'attributes' => [
					'question',
					'answer',
				],
			]) ?>

		<?php endforeach; ?>

	<?php endforeach; ?>

</div>

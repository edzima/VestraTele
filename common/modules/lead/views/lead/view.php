<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\Lead */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="lead-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
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
			'id',
			'source.type',
			'source',
			'status',
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
	'columns' => [
	'type',
	[
	'label' => Yii::t('lead', 'User'),
	'value' => 'user.fullName',
	],
	],
	]) ?>


	<?= GridView::widget([
	'dataProvider' => new ActiveDataProvider(['query' => $model->getReports()->with('schema')]),
	'columns' => [
	'owner',
	'schema',
	'details',
	],
	]) ?>
</div>

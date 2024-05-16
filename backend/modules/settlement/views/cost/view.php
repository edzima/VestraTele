<?php

use backend\modules\settlement\widgets\IssuePayCalculationGrid;
use common\models\issue\IssueCost;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssueCost */

$this->title = $model->typeName;
if ($model->hasUser()) {
	$this->title .= ' - ' . $model->user;
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Costs'), 'url' => ['/settlement/cost/index']];

if ($model->issue) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['/issue/issue/index']];
	$this->params['breadcrumbs'][] = ['label' => $model->issue->longId, 'url' => ['/issue/issue/view', 'id' => $model->issue->id]];
	$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Costs'), 'url' => ['issue', 'id' => $model->issue->id]];
}

$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="issue-cost-view">

	<p>
		<?= !$model->getIsSettled()
			? Html::a(Yii::t('settlement', 'Settle'), ['settle', 'id' => $model->id], ['class' => 'btn btn-success'])
			: ''
		?>

		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<?php $model->getValueWithoutVAT() ?>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			[
				'attribute' => 'user',
				'visible' => $model->user,
			],
			[
				'attribute' => 'valueWithVAT',
				'format' => 'currency',
			],
			[
				'attribute' => 'valueWithoutVAT',
				'format' => 'currency',
			],
			'VATPercent',
			'base_value:currency',
			'date_at:date',
			'deadline_at:date',
			'settled_at:date',
			'confirmed_at:date',
			'created_at:datetime',
			'updated_at:datetime',
		],
	]) ?>

	<?= $model->getHasSettlements() ?
		IssuePayCalculationGrid::widget([
			'withCaption' => true,
			'dataProvider' => new ActiveDataProvider([
				'query' => $model->getSettlements(),
			]),
		])
		: '' ?>
</div>

<?php

use backend\modules\issue\models\search\IssueSearch;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\entityResponsible\EntityResponsible;
use common\widgets\address\AddressDetailView;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\IssueStageColumn;
use common\widgets\grid\IssueTypeColumn;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model EntityResponsible */
/* @var $issueFilterModel IssueSearch */
/* @var $issueDataProvider DataProviderInterface */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Entities responsible'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-entity-responsible-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php if (Yii::$app->user->can(\common\models\user\Worker::PERMISSION_ENTITY_RESPONSIBLE_MANAGER)): ?>
		<p>
			<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger',
				'data' => [
					'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
					'method' => 'post',
				],
			]) ?>
		</p>
	<?php endif; ?>

	<div class="row">
		<div class="col-md-3">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					[
						'attribute' => 'details',
						'format' => 'ntext',
						'visible' => !empty($model->details),
					],
					'is_for_summon:boolean',
				],
			]) ?>
		</div>
		<div class="col-md-3">
			<?= $model->address
				? AddressDetailView::widget([
					'model' => $model->address,
				])
				: ''
			?>
		</div>
	</div>

	<?= GridView::widget([
		'dataProvider' => $issueDataProvider,
		'filterModel' => $issueFilterModel,
		'columns' => [
			[
				'class' => IssueColumn::class,
			],
			[
				'class' => CustomerDataColumn::class,
				'value' => 'customer.fullName',
				'attribute' => 'customerName',
			],
			[
				'class' => IssueTypeColumn::class,
				'attribute' => 'type_id',
				'valueType' => IssueTypeColumn::VALUE_NAME_WITH_SHORT,
			],
			'stage_change_at:date',
			[
				'class' => IssueStageColumn::class,
				'attribute' => 'stage_id',
				'valueType' => IssueStageColumn::VALUE_NAME_WITH_SHORT,
			],
			'updated_at:date',

		],
	]) ?>

</div>

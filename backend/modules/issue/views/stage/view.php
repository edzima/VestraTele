<?php

use backend\modules\issue\models\IssueStage;
use backend\widgets\GridView;
use common\models\issue\IssueStageType;
use common\widgets\grid\ActionColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssueStage */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Stages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);

$typesDataProvider = new ActiveDataProvider([
	'query' => $model->getStageTypes(),
	'pagination' => false,
]);

?>
<div class="issue-stage-view">


	<p>
		<?= Html::a(Yii::t('backend', 'Link with Type'), ['stage-type/create', 'stage_id' => $model->id], ['class' => 'btn btn-success']) ?>

		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this Stage with all Issues with then?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'name',
			'short_name',
			'posi',
		],
	]) ?>


	<p class="clearfix">
		<span class="pull-right">
			<?= $typesDataProvider->totalCount
				? Html::a(
					Yii::t('issue', 'Unlink Types'),
					['stage-type/unlink-stage-types', 'stage_id' => $model->id], [
					'class' => [
						'btn btn-danger',
					],
					'data' => [
						'method' => 'POST',
						'confirm' => Yii::t('issue', 'Are you sure you want to unlink this types?'),
					],
				])
				: ''
			?>
		</span>
	</p>

	<?= GridView::widget([
		'caption' => Yii::t('issue', 'Types'),
		'dataProvider' => $typesDataProvider,
		'emptyText' => Html::a(Yii::t('backend', 'Create'), ['stage-type/create', 'stage_id' => $model->id], ['class' => 'btn btn-success']),
		'columns' => [
			[
				'attribute' => 'typeName',
				'value' => function (IssueStageType $data): string {
					return Html::a(Html::encode($data->getTypeName()), ['type/view', 'id' => $data->type_id]);
				},
				'format' => 'html',
			],
			[
				'attribute' => 'days_reminder',
			],
			[
				'attribute' => 'calendar_background',
				'contentOptions' => static function (IssueStageType $data): array {
					$options = [];
					if (!empty($data->calendar_background)) {
						$options['style']['background-color'] = $data->calendar_background;
					}
					return $options;
				},
			],
			[
				'class' => ActionColumn::class,
				'controller' => 'stage-type',
				'template' => '{update} {delete}',
			],
		],
	]) ?>


</div>

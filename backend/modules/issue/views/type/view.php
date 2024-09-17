<?php

use backend\widgets\GridView;
use common\models\issue\Issue;
use common\models\issue\IssueStageType;
use common\models\issue\IssueType;
use common\models\user\Worker;
use common\widgets\grid\ActionColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssueType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issue Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-type-view">

	<p>

		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_TYPE_PERMISSIONS)
			? Html::a(Yii::t('backend', 'Permissions'), ['permission', 'id' => $model->id], ['class' => 'btn btn-warning'])
			: ''
		?>

		<?= Html::a(Yii::t('backend', 'Link with Stage'), ['stage-type/create', 'type_id' => $model->id], ['class' => 'btn btn-success']) ?>

		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<div class="row">
		<div class="col-md-3">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					[
						'attribute' => 'parentName',
						'value' => function () use ($model): string {
							$name = $model->getParentName();
							if ($name) {
								return Html::a(Html::encode($name), [
									'view', 'id' => $model->parent_id,
								]);
							}
							return '';
						},
						'visible' => $model->parent !== null,
						'format' => 'html',
					],
					'name',
					'short_name',
					'vat',
					'is_main:boolean',
					'with_additional_date:boolean',
					'default_show_linked_notes:boolean',
					'lead_source_id',
				],
			]) ?>
		</div>
		<div class="col-md-4">

			<?= GridView::widget([
				'caption' => Yii::t('issue', 'Child Types'),
				'dataProvider' => new ActiveDataProvider([
					'query' => $model->getChilds(),
					'pagination' => false,
				]),
				'summary' => false,
				'emptyText' => false,
				'showOnEmpty' => false,
				'columns' => [
					'name',
					[
						'class' => ActionColumn::class,
						'template' => '{view} {update} {delete}',
					],
				],
			]) ?>
		</div>

		<div class="col-md-5">
			<?= GridView::widget([
				'caption' => Yii::t('issue', 'Stages'),
				'dataProvider' => new ActiveDataProvider([
					'query' => $model->getTypeStages(),
					'pagination' => false,
				]),
				'summary' => false,
				'emptyText' => false,
				'showOnEmpty' => false,
				'columns' => [
					[
						'attribute' => 'stageName',
						'value' => static function (IssueStageType $data): string {
							return Html::a(Html::encode($data->getStageName()), ['stage/view', 'id' => $data->stage_id]);
						},
						'format' => 'html',
					],
					'days_reminder',
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
						'label' => Yii::t('issue', 'Issues Count'),
						'value' => function (IssueStageType $model): int {
							return Issue::find()
								->type($model->type_id)
								->andWhere(['stage_id' => $model->stage_id])
								->count();
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
	</div>


</div>

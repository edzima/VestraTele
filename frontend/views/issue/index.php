<?php

use common\models\issue\Issue;
use frontend\models\IssueSearch;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sprawy';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php Pjax::begin(); ?>
	<?= $this->render('_search', ['model' => $searchModel]); ?>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'tableOptions' => [
			'class' => 'ellipsis',
		],
		'columns' => [
			['class' => SerialColumn::class],
			[
				'class' => DataColumn::class,
				'attribute' => 'longId',
				'options' => [
					'style' => 'width:100px',
				],

			],
			[
				'class' => DataColumn::class,
				'attribute' => 'type_id',
				'filter' => IssueSearch::getTypesNames(),
				'value' => 'type.short_name',
				'contentOptions' => [
					'class' => 'bold-text text-center',
				],
				'options' => [
					'style' => 'width:80px',
				],

			],
			[
				'class' => DataColumn::class,
				'attribute' => 'stage_id',
				'filter' => $searchModel->getStagesNames(),
				'value' => 'stage.short_name',
				'contentOptions' => [
					'class' => 'bold-text text-center',
				],
				'options' => [
					'style' => 'width:60px',
				],
			],
			[
				'class' => DataColumn::class,
				'filterType' => GridView::FILTER_SELECT2,
				'attribute' => 'agent_id',
				'value' => 'agent',
				'filter' => $searchModel->getAgentsList(),
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => 'Agent',
					],
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'client_surname',
				'value' => 'clientFullName',
				'label' => 'Klient',
				'filterInputOptions' => [
					'class' => 'dynamic-search',
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'victim_surname',
				'value' => 'victimFullName',
				'label' => 'Poszkodowany',
				'filterInputOptions' => [
					'class' => 'dynamic-search',
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'created_at',
				'format' => 'date',
				'width' => '80px',
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'updated_at',
				'width' => '80px',
				'format' => 'date',
			],
			[
				'class' => ActionColumn::class,
				'template' => '{view}',
				'visibleButtons' => [
					'view' => static function (Issue $model) use ($searchModel) {
						return !$model->isArchived() || $searchModel->withArchive;
					},
				],
			],
		],
	]) ?>
	<?php Pjax::end(); ?>
</div>

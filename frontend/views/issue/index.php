<?php

use common\models\issue\Issue;
use common\models\user\Customer;
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
				'attribute' => 'entity_responsible_id',
				'filter' => IssueSearch::getEntityNames(),
				'value' => 'entityResponsible.name',
				'options' => [
					'style' => 'width:200px',
				],
			],
			[
				'class' => DataColumn::class,
				'filterType' => GridView::FILTER_SELECT2,
				'attribute' => 'agent_id',
				'label' => $searchModel->getAttributeLabel('agent_id'),
				'value' => 'agent',
				'filter' => $searchModel->getAgentsList(),
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('agent_id'),
					],
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'customerLastname',
				'value' => 'customer.fullName',
				'label' => Customer::getRolesNames()[Customer::ROLE_CUSTOMER],
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

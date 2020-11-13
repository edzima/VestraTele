<?php

use common\models\issue\IssueUser;
use common\models\user\Customer;
use frontend\models\search\IssueSearch;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Issues');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('frontend', 'Search issue user'), 'user', ['class' => 'btn btn-info']) ?>
	</p>
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
				'attribute' => 'issue_id',
				'options' => [
					'style' => 'width:100px',
				],

			],
			[
				'class' => DataColumn::class,
				'attribute' => 'type_id',
				'label' => $searchModel->getAttributeLabel('type_id'),
				'filter' => IssueSearch::getTypesNames(),
				'value' => 'issue.type.short_name',
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
				'label' => $searchModel->getAttributeLabel('stage_id'),
				'filter' => $searchModel->getStagesNames(),
				'value' => 'issue.stage.short_name',
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
				'label' => $searchModel->getAttributeLabel('entity_responsible_id'),
				'filter' => IssueSearch::getEntityNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('entity_responsible_id'),
					],
				],
				'value' => 'issue.entityResponsible.name',
				'options' => [
					'style' => 'width:200px',
				],
			],
			[
				'class' => DataColumn::class,
				'filterType' => GridView::FILTER_SELECT2,
				'attribute' => 'agent_id',
				'label' => $searchModel->getAttributeLabel('agent_id'),
				'value' => 'issue.agent.fullName',
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
				'value' => 'issue.customer.fullName',
				'label' => Customer::getRolesNames()[Customer::ROLE_CUSTOMER],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'issue.created_at',
				'format' => 'date',
				'width' => '80px',
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'issue.updated_at',
				'width' => '80px',
				'format' => 'date',
			],
			[
				'class' => ActionColumn::class,
				'template' => '{view}',
				'visibleButtons' => [
					'view' => static function (IssueUser $model) use ($searchModel) {
						return !$model->issue->isArchived() || $searchModel->withArchive;
					},
				],
			],
		],
	]) ?>
	<?php Pjax::end(); ?>
</div>

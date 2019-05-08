<?php

use common\models\issue\IssueStage;
use common\models\issue\IssueType;
use common\models\User;
use frontend\models\IssueSearch;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
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
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'tableOptions' => [
			'class' => 'ellipsis',
		],
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'class' => DataColumn::class,
				'attribute' => 'longId',
				'options' => [
					'style' => 'min-width:100px',
				],

			],
			[
				'class' => DataColumn::class,
				'attribute' => 'type_id',
				'filter' => ArrayHelper::map(IssueType::find()->all(), 'id', 'name'),
				'value' => 'type',

			],
			[
				'class' => DataColumn::class,
				'attribute' => 'stage_id',
				'filter' => ArrayHelper::map(IssueStage::find()->all(), 'id', 'name'),
				'value' => 'stage',
			],
			[
				'class' => DataColumn::class,
				'filterType' => GridView::FILTER_SELECT2,
				'attribute' => 'agent_id',
				'value' => 'agent',
				'filter' => ArrayHelper::map(User::find()->with('userProfile')->where(['id' => $searchModel->agents])->all(), 'id', 'fullName'),
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
			],
		],
	]); ?>
	<?php Pjax::end(); ?>
</div>

<?php

use common\models\issue\Issue;
use common\models\issue\IssueSearch;
use common\models\issue\IssueStage;
use common\models\issue\IssueType;
use common\models\User;
use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use kartik\grid\SerialColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Sprawy';
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
	$(document).on('keyup .dynamic-search', function() {
	  	setTimeout(function(){
			$("#issues-list").yiiGridView("applyFilter");
	
	},500);
	});
JS;
$this->registerJs($js);

?>
<div class="issue-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php Pjax::begin(); ?>
	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'id' => 'issues-list',
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'responsive' => false,
		'tableOptions' => [
			'class' => 'table-fixed-layout',
		],
		//'pjax' => true,
		'columns' => [
			[
				'class' => SerialColumn::class,
				'width' => '40px',
			],
			[
				'class' => ActionColumn::class,
				'template' => '{view} {update} {pay} {delete}',
				'buttons' => [
					'pay' => function ($url, Issue $model, $key) {
						if ($model->isPayed() || !Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
							return '';
						}
						return Html::a(
							'<span class="glyphicon glyphicon-usd" aria-hidden="true"></span>',
							Url::toRoute(['pay/create', 'issueId' => $model->id]),
							[
								'title' => 'Wpłata',
								'aria-label' => 'Wpłata',
							]);
					},
				],
				'noWrap' => true,

			],
			[
				'class' => DataColumn::class,
				'attribute' => 'id',
				'value' => 'longId',
				'noWrap' => true,
			],
			[
				'class' => DataColumn::class,
				'filterType' => GridView::FILTER_SELECT2,
				'attribute' => 'agent_id',
				'value' => 'agent',
				'filter' => User::getSelectList([User::ROLE_AGENT]),
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
				'attribute' => 'type_id',
				'filter' => ArrayHelper::map(IssueType::find()->all(), 'id', 'nameWithShort'),
				'value' => 'type.short_name',
				'width' => '50px',
				'contentOptions' => [
					'class' => 'bold-text text-center',
				],

			],
			[
				'class' => DataColumn::class,
				'attribute' => 'stage_id',
				'filter' => ArrayHelper::map(IssueStage::find()->all(), 'id', 'nameWithShort'),
				'value' => 'stage.short_name',
				'width' => '50px',
				'contentOptions' => [
					'class' => 'bold-text text-center',
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
				'contentOptions' => [
					'class' => 'bold-text',
				],
				'noWrap' => true,
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'updated_at',
				'format' => 'date',
				'contentOptions' => [
					'class' => 'bold-text',
				],
				'noWrap' => true,
			],

			[
				'label' => 'Płatność',
				'attribute' => 'payStatus',
				'filter' => IssueSearch::payStatuses(),
				'visible' => Yii::$app->user->can(User::ROLE_BOOKKEEPER),
			],

		],
	]); ?>
	<?php Pjax::end(); ?>
</div>

<?php

use common\models\issue\Issue;
use common\models\issue\IssueSearch;
use common\models\User;
use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use kartik\grid\SerialColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Sprawy';
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
	var timeout;
	var filteredId = '';
	$(document).on('keyup', function(evt) {
		if(evt.target.classList.contains('dynamic-search')){
			clearTimeout(timeout);
			timeout = setTimeout(function(){
				filteredId = evt.target.getAttribute('id');
				evt.target.classList.add('filtered');
				$("#issues-list").yiiGridView("applyFilter");
			},500);
		}

	});

	$(document).on('pjax:success', function() {
		if(filteredId.length){
			var input = document.getElementById(filteredId);
			input.focus();
			var val = input.value;
			input.value = ''; 
			input.value = val; 
			filteredId = '';
		}
	}); 
JS;
$this->registerJs($js);

?>
<div class="issue-index">
	<?php Pjax::begin(); ?>
	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'id' => 'issues-list',
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'resizableColumns' => false,
		'tableOptions' => [
			'class' => 'table-fixed-layout',
		],
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
				'options' => [
					'style' => 'width:110px',
				],

			],
			[
				'class' => DataColumn::class,
				'attribute' => 'id',
				'value' => 'longId',
				'filterInputOptions' => [
					'class' => 'dynamic-search',
				],
				'options' => [
					'style' => 'width:100px',
				],
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
				'contentOptions' => [
					'class' => 'ellipsis',
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
				'filter' => IssueSearch::getStagesNames(),
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
				'attribute' => 'client_surname',
				'value' => 'clientFullName',
				'label' => 'Klient',
				'filterInputOptions' => [
					'class' => 'dynamic-search',
				],
				'contentOptions' => [
					'class' => 'ellipsis',
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
				'contentOptions' => [
					'class' => 'ellipsis',
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'created_at',
				'format' => 'date',
				'contentOptions' => [
					'class' => 'bold-text',
				],
				'options' => [
					'style' => 'width:90px',
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'updated_at',
				'format' => 'date',
				'contentOptions' => [
					'class' => 'bold-text',
				],
				'options' => [
					'style' => 'width:90px',
				],
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

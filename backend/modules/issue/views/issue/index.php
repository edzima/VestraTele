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
//@todo move to external class and use in frontend.
function moveCursorToEnd(el) {
	if (typeof el.selectionStart == "number") {
		el.selectionStart = el.selectionEnd = el.value.length;
	} else if (typeof el.createTextRange != "undefined") {
		el.focus();
		var range = el.createTextRange();
		range.collapse(false);
		range.select();
	}
}
	var timeout;
	var filteredId = '';
	var submit_form = true;
	var filter_selector = '.dynamic-search';

	
	$("body").on('beforeFilter', "#issues-list" , function(event) {
    return submit_form;
});

$("body").on('afterFilter', "#issues-list" , function(event) {
    submit_form = false;
});
	
	$(document)
.off('keydown.yiiGridView change.yiiGridView', filter_selector)
.on('keyup', filter_selector, function(evt) {
   clearTimeout(timeout);
			timeout = setTimeout(function(){
				submit_form = true;
				filteredId = evt.target.getAttribute('id');
				$("#issues-list").yiiGridView("applyFilter");
			},500);
})
.on('pjax:success', function() {
	submit_form = true;
	var i = document.getElementById(filteredId);
	if(i){
		i.focus();
		moveCursorToEnd(i);
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

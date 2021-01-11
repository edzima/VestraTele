<?php

use backend\modules\issue\models\search\IssueSearch;
use backend\widgets\CsvForm;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\Issue;
use common\models\user\User;
use common\models\user\Worker;
use common\widgets\grid\CustomerDataColumn;
use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use kartik\grid\SerialColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('backend', 'Issues');
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
			},1000);
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
//$this->registerJs($js);

?>
<div class="issue-index relative">
	<?php Pjax::begin(); ?>

	<p>
		<?= Yii::$app->user->can(User::PERMISSION_SUMMON)
			? Html::a(Yii::t('common', 'Summons'), ['/issue/summon/index'], ['class' => 'btn btn-warning'])
			: ''
		?>
		<?= Yii::$app->user->can(User::ROLE_BOOKKEEPER)
			? Html::a(Yii::t('backend', 'Settlements'), ['/settlement/calculation/index'], ['class' => 'btn btn-success'])
			: ''
		?>
	</p>

	<?= $this->render('_search', ['model' => $searchModel]) ?>
	<?= Yii::$app->user->can(User::PERMISSION_EXPORT) ? CsvForm::widget() : '' ?>

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
				'noWrap' => true,
				'options' => [
					'style' => 'width:110px',
				],
				'visibleButtons' => [
					'view' => static function (Issue $model) use ($searchModel): bool {
						return !$model->isArchived() || $searchModel->withArchive;
					},
					'delete' => Yii::$app->user->can(User::ROLE_ADMINISTRATOR),
				],
			],
			[
				'class' => IssueColumn::class,
				'issueAttribute' => null,
			],
			[
				'class' => DataColumn::class,
				'filterType' => GridView::FILTER_SELECT2,
				'attribute' => 'agent_id',
				'value' => 'agent',
				'label' => Worker::getRolesNames()[Worker::ROLE_AGENT],
				'filter' => Worker::getSelectList([Worker::ROLE_AGENT]),
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('agent_id'),
					],
				],
				'contentOptions' => [
					'class' => 'ellipsis',
				],
				'options' => [
					'style' => 'width:200px',
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
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('entity_responsible_id'),
					],
				],
				'value' => 'entityResponsible.name',
				'options' => [
					'style' => 'width:200px',
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'stage_change_at',
				'format' => 'date',
				'contentOptions' => [
					'class' => 'bold-text',
				],
				'options' => [
					'style' => 'width:90px',
				],
				'visible' => $searchModel->onlyDelayed,
			],
			[
				'class' => CustomerDataColumn::class,
				'value' => 'customer.fullName',
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

		],
	]); ?>
	<?php Pjax::end(); ?>
</div>

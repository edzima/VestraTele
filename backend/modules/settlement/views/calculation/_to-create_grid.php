<?php

use backend\helpers\Url;
use backend\modules\settlement\models\search\IssueToCreateCalculationSearch;
use backend\widgets\GridView;
use common\models\issue\Issue;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\DataColumn;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $searchModel IssueToCreateCalculationSearch */
/* @var $dataProvider ActiveDataProvider */
/* @var $withIssue bool */
/* @var $withCustomer bool */
?>


<?= $dataProvider ? GridView::widget([
	'id' => 'to-create-grid',
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'columns' => [
		[
			'class' => ActionColumn::class,
			'template' => '{create}',
			'buttons' => [
				'create' => static function ($url, Issue $model): string {
					return Html::a(
						'<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
						Url::toRoute(['create', 'id' => $model->id]),
						[
							'title' => 'Dodaj',
							'aria-label' => 'Dodaj',
						]);
				},
			],
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'issue_id',
			'value' => 'longId',
			'options' => [
				'style' => 'width:100px',
			],
			'visible' => $withIssue,
		],
		[
			'attribute' => 'type.name',
			'filter' => IssueToCreateCalculationSearch::getTypesNames(),
		],
		[
			'attribute' => 'stage.name',
			'filter' => $searchModel->getStagesNames(),
		],
		[
			'class' => CustomerDataColumn::class,
			'visible' => $withCustomer,
		],
	],
]) : '' ?>

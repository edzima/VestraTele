<?php

use backend\helpers\Url;
use backend\modules\settlement\models\search\IssueToCreateCalculationSearch;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\Issue;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerDataColumn;
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
			'class' => IssueColumn::class,
			'issueAttribute' => null,
			'visible' => $withIssue,
		],
		[
			'attribute' => 'type',
			'value' => 'type.name',
			'filter' => IssueToCreateCalculationSearch::getTypesNames(),
		],
		[
			'attribute' => 'stage',
			'value' => 'stage.name',
			'filter' => $searchModel->getStagesNames(),
		],
		[
			'class' => CustomerDataColumn::class,
			'visible' => $withCustomer,
		],
	],
]) : '' ?>

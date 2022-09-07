<?php

use backend\helpers\Url;
use backend\modules\settlement\models\search\IssueToCreateCalculationSearch;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\Issue;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\IssueTypeColumn;
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
						Html::icon('plus'),
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
			'visible' => $withIssue,
		],
		[
			'class' => CustomerDataColumn::class,
			'value' => 'customer.fullName',
			'attribute' => 'customerName',
			'visible' => $withCustomer,
		],
		[
			'attribute' => 'type_id',
			'class' => IssueTypeColumn::class,
		],
		[
			'attribute' => 'stage_id',
			'value' => 'stage.name',
			'filter' => $searchModel->getStagesNames(),
		],

	],
]) : '' ?>

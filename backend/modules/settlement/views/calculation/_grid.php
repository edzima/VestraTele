<?php

use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerDataColumn;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $searchModel IssuePayCalculationSearch */
/* @var $dataProvider ActiveDataProvider */
/* @var $withIssue bool */
/* @var $withCustomer bool */
/* @var $withProblemStatus bool */
?>

<?= GridView::widget([
	'id' => 'calculations-grid',
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'columns' => [
		[
			'class' => ActionColumn::class,
			'template' => '{problem-status} {provision} {view} {update} {delete}',
			'buttons' => [
				'problem-status' => static function (string $url, IssuePayCalculation $model): string {
					if ($model->isPayed()) {
						return '';
					}
					return Html::a(Html::icon('warning'), $url);
				},
				'provision' => static function (string $url, IssuePayCalculation $model) {
					return Yii::$app->user->can(User::PERMISSION_PROVISION) ? Html::a('<span class="glyphicon glyphicon-usd"></span>',
						['/provision/settlement/set', 'id' => $model->id],
						[
							'title' => 'Prowizje',
							'aria-label' => 'Prowizje',
							'data-pjax' => '0',
						])
						: '';
				},

			],
		],
		[
			'class' => IssueColumn::class,
			'visible' => $withIssue,
		],
		[
			'attribute' => 'type',
			'value' => 'typeName',
			'filter' => IssuePayCalculationSearch::getTypesNames(),
		],
		[
			'attribute' => 'problem_status',
			'value' => 'problemStatusName',
			'filter' => IssuePayCalculationSearch::getProblemStatusesNames(),
			'visible' => $withProblemStatus,
		],
		[
			'class' => CustomerDataColumn::class,
			'visible' => $withCustomer,
		],
		[
			'attribute' => 'providerName',
			'filter' => IssuePayCalculationSearch::getProvidersTypesNames(),
		],
		[
			'attribute' => 'value',
			'format' => 'currency',
		],
		[
			'attribute' => 'created_at',
			'format' => 'date',
		],
		[
			'attribute' => 'updated_at',
			'format' => 'date',
		],
		[
			'attribute' => 'payment_at',
			'format' => 'date',
		],
	],
]) ?>

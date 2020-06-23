<?php

use backend\helpers\Url;
use common\models\issue\Issue;
use backend\modules\issue\models\searches\IssuePayCalculationSearch;
use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\bootstrap\Nav;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel IssuePayCalculationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rozliczenia';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-pay-calculation-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<p>
		<?= Nav::widget([
			'items' => [
				[
					'label' => 'Nowe',
					'url' => ['index', 'onlyNew' => true],
					'active' => $searchModel->isOnlyNew(),
				],
				[
					'label' => IssuePayCalculationSearch::getStatusNames()[IssuePayCalculationSearch::STATUS_ACTIVE],
					'url' => ['index'],
					'active' => $searchModel->isActive(),
				],
				[
					'label' => IssuePayCalculationSearch::getStatusNames()[IssuePayCalculationSearch::STATUS_PAYED],
					'url' => ['index', 'status' => IssuePayCalculationSearch::STATUS_PAYED],
					'active' => $searchModel->isPayed(),
				],
				[
					'label' => IssuePayCalculationSearch::getStatusNames()[IssuePayCalculationSearch::STATUS_BEFORE_LAWSUIT],
					'url' => ['index', 'status' => IssuePayCalculationSearch::STATUS_BEFORE_LAWSUIT],
					'active' => $searchModel->isBeforeLawsuit(),
				],
				[
					'label' => IssuePayCalculationSearch::getStatusNames()[IssuePayCalculationSearch::STATUS_LAWSUIT],
					'url' => ['index', 'status' => IssuePayCalculationSearch::STATUS_LAWSUIT],
					'active' => $searchModel->isLawsuit(),
				],
				[
					'label' => IssuePayCalculationSearch::getStatusNames()[IssuePayCalculationSearch::STATUS_BAILIFF],
					'url' => ['index', 'status' => IssuePayCalculationSearch::STATUS_BAILIFF],
					'active' => $searchModel->isBailiff(),
				],
				[
					'label' => IssuePayCalculationSearch::getStatusNames()[IssuePayCalculationSearch::STATUS_DRAFT],
					'url' => ['index', 'status' => IssuePayCalculationSearch::STATUS_DRAFT],
					'active' => $searchModel->isDraft(),
				],

			],
			'options' => ['class' => 'nav-pills'],
		]) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
	<?php
	$idColumn = [

		'attribute' => 'id',
		'format' => 'raw',
		'label' => 'Sprawa',
		'value' => static function (Issue $model) {
			return Html::a(
				$model,
				Url::issueView($model->id),
				['target' => '_blank']);
		},

	];
	$client =
		[
			'class' => DataColumn::class,
			'attribute' => 'clientSurname',
			'value' => 'clientFullName',
			'label' => 'Klient',
			'filterInputOptions' => [
				'class' => 'dynamic-search',
			],
			'contentOptions' => [
				'class' => 'ellipsis',
			],
		];

	?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => $searchModel->isOnlyNew()
			? [
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
				$idColumn,
				$client,

			]
			: [
				[
					'class' => ActionColumn::class,
				],
				$idColumn,
				$client,
				[
					'attribute' => 'cityName',
					'format' => 'raw',
					'label' => 'Miejscowość',
					'value' => static function (Issue $model) {
						if ($model->payCity !== null) {
							return Html::a(
								Html::encode($model->payCity->city),
								Url::payCityDetails($model->pay_city_id),
								['target' => '_blank']);
						}
					},
				],
				[
					'attribute' => 'value',
					'value' => static function (Issue $model): string {
						return $model->payCalculation->value;
					},
					'label' => 'Kwota',
					'format' => 'decimal',
				],
				[

					'attribute' => 'pay_type',
					'value' => static function (Issue $model): string {
						if ($model->hasPayCalculation()) {
							return $model->payCalculation->getPayName();
						}
						return '';
					},
					'filter' => IssuePayCalculationSearch::getPayTypesNames(),
					'label' => 'Preferowana płatność',
				],
				[
					'attribute' => 'created_at',
					'value' => 'payCalculation.created_at',
					'format' => 'date',
				],
				[
					'attribute' => 'updated_at',
					'value' => 'payCalculation.updated_at',
					'format' => 'date',
				],

			]

		,
	]) ?>


</div>

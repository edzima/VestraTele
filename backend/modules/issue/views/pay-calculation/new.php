<?php

use backend\helpers\Url;
use backend\modules\issue\models\search\NewPayCalculationSearch;
use common\models\issue\Issue;
use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel NewPayCalculationSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Rozliczenia (nowe)';
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenia', 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="issue-pay-calculation-new">
	<h1><?= Html::encode($this->title) ?></h1>

	<?= GridView::widget([
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
				'attribute' => 'id',
				'format' => 'raw',
				'label' => 'Sprawa',
				'value' => static function (Issue $model) {
					return Html::a(
						$model,
						Url::issueView($model->id),
						['target' => '_blank']);
				},
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
		],
	]) ?>
</div>

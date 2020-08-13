<?php

use backend\helpers\Url;
use backend\modules\issue\models\searches\IssuePayCalculationSearch;
use common\models\issue\IssuePayCalculation;
use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel IssuePayCalculationSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Rozliczenia';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-pay-calculation-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Nowe',
			$searchModel->issue_id
				? ['create', 'id' => $searchModel->issue_id]
				: ['new'],
			[
				'class' => 'btn btn-success',
			]) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => ActionColumn::class,
			],
			[
				'attribute' => 'issue_id',
				'format' => 'raw',
				'label' => 'Sprawa',
				'value' => static function (IssuePayCalculation $model) {
					return Html::a(
						$model->issue,
						Url::issueView($model->issue_id),
						['target' => '_blank']);
				},
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => IssuePayCalculationSearch::getTypesNames(),
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'client_surname',
				'value' => 'issue.clientFullName',
				'label' => 'Klient',
				'filterInputOptions' => [
					'class' => 'dynamic-search',
				],
				'contentOptions' => [
					'class' => 'ellipsis',
				],
			],
			[
				'attribute' => 'value',
				'label' => 'Kwota',
				'format' => 'decimal',
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

		]

		,
	]) ?>


</div>

<?php

use backend\helpers\Url;
use backend\modules\issue\models\searches\IssuePaySearch;
use common\models\issue\IssuePay;
use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use kartik\grid\SerialColumn;
use yii\bootstrap\Nav;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel IssuePaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wpłaty';
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenia', 'url' => ['pay-calculation/index']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-pay-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?php
		$statusItems = [];
		foreach (IssuePaySearch::getStatusNames() as $status => $name) {
			$statusItems[] = [
				'label' => $name,
				'url' => ['index', 'status' => $status],
				'active' => $searchModel->getStatus() === $status,
			];
		}
		?>
		<?= Nav::widget([
			'items' => $statusItems,
			'options' => ['class' => 'nav-pills'],
		]) ?>


	</p>


	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?php if ($dataProvider->totalCount > 0): ?>
		<div class="pay-summary-wrap">
			<h4>Podsumowanie płatności</h4>
			<ul>
				<li>Należna: <?= $searchModel->getValueSum($dataProvider->query) ?></li>
				<li>Zapłacono: <?= $searchModel->getPayedSum($dataProvider->query) ?></li>
				<li>Niezaplacono: <?= $searchModel->getNotPaySum($dataProvider->query) ?></li>
			</ul>
		</div>
	<?php endif; ?>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'summary' => false,
		'showPageSummary' => true,
		'hover' => true,
		'columns' => [

			['class' => SerialColumn::class],
			[
				'class' => ActionColumn::class,
				'template' => '{pay}',
				'buttons' => [
					'pay' => static function ($url, IssuePay $model): string {

						return Html::a(
							'<span class="glyphicon glyphicon-check" aria-hidden="true"></span>',
							Url::toRoute(['pay', 'id' => $model->id]),
							[
								'title' => 'Oplac',
								'aria-label' => 'Oplac',
								'target' => '_blank',
							]);
					},
				],
			],
			[
				'class' => DataColumn::class,

				'attribute' => 'issue_id',
				'format' => 'raw',
				'value' => function (IssuePay $model) {
					return Html::a(
						$model->issue,
						Url::toRoute(['pay-calculation/view', 'id' => $model->issue_id]),
						['target' => '_blank']);
				},
				'filterInputOptions' => [
					'class' => 'dynamic-search',
				],
				'width' => '50px',

			],
			[
				'class' => DataColumn::class,
				'attribute' => 'clientSurname',
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
				'class' => DataColumn::class,
				'attribute' => 'value',
				'format' => 'currency',
				'pageSummary' => true,
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'valueNetto',
				'format' => 'currency',
				'pageSummary' => true,
			],
			'vatPercent:percent',
			[
				'attribute' => 'partInfo',
			],
			[
				'attribute' => 'deadline_at',
				'value' => static function (IssuePay $model) {
					if ($model->deadline_at > 0) {
						return $model->deadline_at;
					}
				},
				'format' => 'date',
			],

		],
	]); ?>
</div>

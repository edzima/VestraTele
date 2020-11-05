<?php

use backend\helpers\Url;
use backend\modules\issue\models\search\IssuePaySearch;
use backend\widgets\CsvForm;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\user\Worker;
use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use kartik\grid\SerialColumn;
use yii\bootstrap\Nav;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel IssuePaySearch */
/* @var $dataProvider ActiveDataProvider */
/* @var $withMenu bool */

$this->title = 'Wpłaty';
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenia', 'url' => ['pay-calculation/index']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-pay-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?php if ($withMenu): ?>
			<?php
			$statusItems = [];
			foreach (IssuePaySearch::getPayStatusNames() as $status => $name) {
				$statusItems[] = [
					'label' => $name,
					'url' => ['index', 'status' => $status],
					'active' => $searchModel->getPayStatus() === $status,
				];
			}
			?>
			<?= Nav::widget([
				'items' => $statusItems,
				'options' => ['class' => 'nav-pills'],
			]) ?>
		<?php endif; ?>

	</p>


	<?= $this->render('_search', ['model' => $searchModel]) ?>
	<?= CsvForm::widget() ?>


	<?php if ($dataProvider->totalCount > 0): ?>
		<div class="pay-summary-wrap">
			<h4>Podsumowanie płatności</h4>
			<ul>
				<li>
					Należna: <?= Yii::$app->formatter->asCurrency($searchModel->getValueSum($dataProvider->query)) ?></li>
				<li>
					Zapłacono: <?= Yii::$app->formatter->asCurrency($searchModel->getPayedSum($dataProvider->query)) ?></li>
				<li>
					Niezaplacono: <?= Yii::$app->formatter->asCurrency($searchModel->getNotPaySum($dataProvider->query)) ?></li>
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
				'template' => '{pay}{status}',
				'buttons' => [
					'pay' => static function ($url, IssuePay $model): string {
						if (Yii::$app->user->can(Worker::ROLE_BOOKKEEPER)) {
							return Html::a(
								'<span class="glyphicon glyphicon-check" aria-hidden="true"></span>',
								Url::toRoute(['pay', 'id' => $model->id]),
								[
									'title' => 'Oplac',
									'aria-label' => 'Oplac',
									'target' => '_blank',
								]);
						}
						return '';
					},
					'status' => static function ($url, IssuePay $model): string {
						return Html::a(
							'<span class="glyphicon glyphicon-flag" aria-hidden="true"></span>',
							Url::toRoute(['status', 'id' => $model->id]),
							[
								'title' => 'Status',
								'aria-label' => 'Status',
								'target' => '_blank',
							]);
					},
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'issue_id',
				'format' => 'raw',
				'value' => static function (IssuePay $model) {
					return Html::a(
						$model->issue,
						Url::toRoute(['pay-calculation/view', 'id' => $model->calculation_id]),
						['target' => '_blank']);
				},
				'filterInputOptions' => [
					'class' => 'dynamic-search',
				],
				'width' => '50px',
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'calculationType',
				'value' => 'calculation.typeName',
				'filter' => IssuePayCalculation::getTypesNames(),
				'label' => 'Rozliczenie',
				'width' => '100px',
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => IssuePay::getStatusNames(),
				'visible' => !$searchModel->isActive(),
				'width' => '100px',
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'clientSurname',
				'value' => 'issue.client',
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
				'value' => static function (IssuePay $model): ?string {
					if ($model->deadline_at > 0) {
						return $model->deadline_at;
					}
					return null;
				},
				'format' => 'date',
			],

		],
	]); ?>
</div>

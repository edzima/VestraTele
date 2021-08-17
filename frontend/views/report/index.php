<?php

use common\models\provision\ProvisionReportSearch;
use common\widgets\grid\CustomerDataColumn;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;

/* @var $this View */
/* @var $searchModel ProvisionReportSearch */
/* @var $dataProvider ActiveDataProvider */

if ($searchModel->to_user_id === Yii::$app->user->getId()) {
	$this->title = Yii::t('provision', 'Provisions Report ({from} - {to})', [
		'from' => Yii::$app->formatter->asDate($searchModel->dateFrom),
		'to' => Yii::$app->formatter->asDate($searchModel->dateTo),
	]);
} else {
	$this->title = Yii::t('provision',
		'Provisions Report: {user} ({from} - {to})', [
			'user' => $searchModel->toUser->getFullName(),
			'from' => Yii::$app->formatter->asDate($searchModel->dateFrom),
			'to' => Yii::$app->formatter->asDate($searchModel->dateTo),
		]);
}
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_search', ['model' => $searchModel]) ?>


<?= GridView::widget([
	'id' => 'report-grid',
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'showPageSummary' => true,

	'columns' => [
		[
			'attribute' => 'pay.issue',
			'label' => 'Nr sprawy',
		],
		[
			'class' => CustomerDataColumn::class,
			'value' => 'pay.issue.customer.fullName',
		],
		'fromUserString',
		[
			'label' => 'Płatność',
			'value' => 'pay.partInfo',
		],
		[
			'label' => 'Typ',
			'value' => 'type.name',
		],
		[
			'label' => 'Wpłata (netto)',
			'value' => 'pay.valueNetto',
			'format' => 'currency',
		],
		'provision:percent',
		[
			'class' => DataColumn::class,
			'attribute' => 'value',
			'format' => 'currency',
			'pageSummary' => true,
		],
	],
]) ?>


<?php if ($dataProvider->pagination->pageCount > 1): ?>
	<p>Suma: <?= $searchModel->getSum($dataProvider->query) ?></p>
<?php endif; ?>




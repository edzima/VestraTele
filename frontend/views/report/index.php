<?php

use common\models\provision\ProvisionReportSearch;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;

/* @var $this View */
/* @var $searchModel ProvisionReportSearch */
/* @var $dataProvider ActiveDataProvider */
$this->title = 'Raport: ' . $searchModel->toUser . ' (' . Yii::$app->formatter->asDate($searchModel->dateFrom) . ' - ' . Yii::$app->formatter->asDate($searchModel->dateTo) . ')';
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
			'attribute' => 'pay.issue.clientFullName',
			'label' => 'Klient',
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




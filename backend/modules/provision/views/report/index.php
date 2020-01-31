<?php

use backend\helpers\Url;
use common\models\provision\Provision;
use common\models\provision\ProvisionUsersSearch;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel ProvisionUsersSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Raporty';
$this->params['breadcrumbs'][] = $this->title;
$dateFrom = $searchModel->dateFrom;
$dateTo = $searchModel->dateTo;
?>
<div class="provision-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'showPageSummary' => true,
		'columns' => [
			[
				'attribute' => 'toUser',
				'value' => static function (Provision $provision) use ($dateTo, $dateFrom): string {
					return Html::a($provision->toUser, Url::to(['view', 'id' => $provision->to_user_id, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo]));
				},
				'format' => 'raw',
			],
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


</div>

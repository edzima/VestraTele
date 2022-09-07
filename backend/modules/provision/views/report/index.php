<?php

use backend\helpers\Url;
use backend\widgets\GridView;
use common\models\provision\Provision;
use common\models\provision\ToUserGroupProvisionSearch;
use common\widgets\grid\CurrencyColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel ToUserGroupProvisionSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('provision', 'Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['provision/index']];
$this->params['breadcrumbs'][] = $this->title;
$dateFrom = $searchModel->dateFrom;
$dateTo = $searchModel->dateTo;
?>
<div class="provision-index">

	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'showPageSummary' => true,
		'columns' => [
			[
				'label' => Yii::t('provision', 'User'),
				'attribute' => 'toUser',
				'value' => static function (Provision $provision) use ($dateTo, $dateFrom): string {
					return Html::a($provision->toUser, Url::to(['view', 'id' => $provision->to_user_id, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo]));
				},
				'format' => 'raw',
			],
			[
				'class' => CurrencyColumn::class,
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
